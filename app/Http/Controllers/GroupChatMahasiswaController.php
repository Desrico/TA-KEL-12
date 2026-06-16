<?php

namespace App\Http\Controllers;

use App\Events\GroupChatMessageSent;
use App\Models\GroupChatMember;
use App\Models\GroupChatMessage;
use App\Models\GroupChatRoom;
use App\Models\User;
use App\Support\GroupChatSupport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GroupChatMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $eligibility = GroupChatSupport::syncMemberEligibilityStatus($user);

        if ($request->filled('group')) {
            return redirect()->route('mahasiswa.group-chat.room', ['group' => $request->integer('group')]);
        }

        return view('Pages.group-chat', [
            'joinedRooms' => $this->resolveJoinedRooms($user),
            'pendingInvitations' => $this->resolvePendingInvitations($user),
            'publicTopics' => $this->resolvePublicTopicCards($user),
            'groupRules' => GroupChatSupport::rules(),
            'eligibilityNotice' => $eligibility['eligible'] ? null : $eligibility['reason'],
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $eligibility = GroupChatSupport::syncMemberEligibilityStatus($user);

        if (! $eligibility['eligible']) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', $eligibility['reason']);
        }

        $topic = $request->query('topic');
        $selectedTopic = null;

        if ($topic && array_key_exists($topic, GroupChatRoom::topicOptions())) {
            $selectedTopic = collect($this->resolvePublicTopicCards($user))->firstWhere('topic_key', $topic);
        }

        return view('Pages.group-chat-create', [
            'publicTopics' => $this->resolvePublicTopicCards($user),
            'pendingInvitations' => $this->resolvePendingInvitations($user),
            'groupRules' => GroupChatSupport::rules(),
            'consentVersion' => GroupChatSupport::consentVersion(),
            'consentContext' => $selectedTopic ? $this->buildPublicConsentContext($selectedTopic) : null,
        ]);
    }

    public function invitation(Request $request, string $token)
    {
        if (! GroupChatSupport::supportsPrivateGroups()) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Undangan grup privat belum aktif karena pembaruan database group chat belum dijalankan.');
        }

        $user = $request->user();
        $eligibility = GroupChatSupport::syncMemberEligibilityStatus($user);

        if (! $eligibility['eligible']) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', $eligibility['reason']);
        }

        $room = GroupChatRoom::query()
            ->withCount([
                'members as active_members_count' => fn ($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
            ])
            ->where('invite_token', $token)
            ->where('visibility', GroupChatRoom::VISIBILITY_PRIVATE)
            ->where('is_active', true)
            ->first();

        if (! $room) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Undangan grup privat tidak ditemukan atau sudah tidak aktif.');
        }

        try {
            // Undangan privat harus tetap terikat ke mahasiswa yang memang diundang, bukan token saja.
            $membership = $this->resolvePrivateInvitationMembership($user, $room);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', collect($exception->errors())->flatten()->first() ?: 'Anda tidak memiliki akses ke undangan grup privat ini.');
        }

        if ($membership && (! GroupChatSupport::supportsMembershipStatus() || $membership->membership_status === GroupChatMember::STATUS_ACTIVE)) {
            return redirect()
                ->route('mahasiswa.group-chat.room', ['group' => $room->id])
                ->with('success', 'Anda sudah tergabung di grup privat tersebut.');
        }

        return view('Pages.group-chat-create', [
            'publicTopics' => $this->resolvePublicTopicCards($user),
            'pendingInvitations' => $this->resolvePendingInvitations($user),
            'groupRules' => GroupChatSupport::rules(),
            'consentVersion' => GroupChatSupport::consentVersion(),
            'consentContext' => $this->buildPrivateConsentContext($room, $membership),
        ]);
    }

    public function room(Request $request, int $group)
    {
        $user = $request->user();
        $eligibility = GroupChatSupport::syncMemberEligibilityStatus($user);

        if (! $eligibility['eligible']) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', $eligibility['reason']);
        }

        $joinedRooms = $this->resolveJoinedRooms($user);
        $activeRoom = $this->resolveAccessibleRoom($user, $group);

        if (! $activeRoom) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Anda belum tergabung aktif di grup chat tersebut.');
        }

        $messages = $activeRoom->messages
            ->sortBy('created_at')
            ->values()
            ->map(fn (GroupChatMessage $message) => $this->transformMessage($message, $user, $activeRoom))
            ->all();

        return view('Pages.group-chat-room', [
            'joinedRooms' => $joinedRooms,
            'activeRoom' => $activeRoom,
            'chatPayload' => $this->buildChatPayload($activeRoom, $messages),
        ]);
    }

    public function join(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['nullable', 'integer'],
            'topic' => ['nullable', 'string', Rule::in(array_keys(GroupChatRoom::topicOptions()))],
            'invite_token' => ['nullable', 'string'],
            'consent_acknowledged' => ['required', 'accepted'],
        ]);

        $user = $request->user();
        $eligibility = GroupChatSupport::syncMemberEligibilityStatus($user);

        if (! $eligibility['eligible']) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', $eligibility['reason']);
        }

        try {
            if (! empty($validated['invite_token'])) {
                if (! GroupChatSupport::supportsPrivateGroups()) {
                    return redirect()
                        ->route('mahasiswa.group-chat')
                        ->with('error', 'Undangan grup privat belum aktif karena pembaruan database group chat belum dijalankan.');
                }

                [$room, $member] = $this->joinPrivateRoomByInvite($user, (string) $validated['invite_token']);

                return redirect()
                    ->route('mahasiswa.group-chat.room', ['group' => $room->id])
                    ->with(
                        'success',
                        $member->wasRecentlyCreated || (GroupChatSupport::supportsMembershipStatus() && $member->getOriginal('membership_status') !== GroupChatMember::STATUS_ACTIVE)
                            ? 'Anda berhasil menerima undangan grup privat.'
                            : 'Ruang chat grup privat dibuka kembali.'
                    );
            }

            [$room, $member, $topicLabel] = $this->joinPublicRoom($user, $validated);

            return redirect()
                ->route('mahasiswa.group-chat.room', ['group' => $room->id])
                ->with(
                    'success',
                    $member->wasRecentlyCreated || (GroupChatSupport::supportsMembershipStatus() && $member->getOriginal('membership_status') !== GroupChatMember::STATUS_ACTIVE)
                        ? 'Anda berhasil masuk ke grup konseling ' . $topicLabel . '.'
                        : 'Ruang chat grup konseling ' . $topicLabel . ' dibuka kembali.'
                );
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::warning('Join group chat mahasiswa gagal diproses.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->with('error', 'Grup chat tidak dapat diproses saat ini. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function messages(Request $request): JsonResponse
    {
        $room = $this->resolveAccessibleRoom($request->user(), $request->integer('group_id'));

        if (! $room) {
            return response()->json([
                'success' => false,
                'message' => 'Grup chat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'messages' => $room->messages
                ->sortBy('created_at')
                ->values()
                ->map(fn (GroupChatMessage $message) => $this->transformMessage($message, $request->user(), $room))
                ->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'pesan' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $eligibility = GroupChatSupport::syncMemberEligibilityStatus($user);

        if (! $eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['reason'],
            ], 403);
        }

        $room = $this->resolveAccessibleRoom($user, (int) $validated['group_id']);

        if (! $room) {
            return response()->json([
                'success' => false,
                'message' => 'Grup chat tidak ditemukan.',
            ], 404);
        }

        $message = DB::transaction(function () use ($room, $user, $validated) {
            return GroupChatMessage::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'pesan' => trim($validated['pesan']),
            ]);
        });

        $message->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
            'room',
        ]);

        try {
            broadcast(new GroupChatMessageSent($message))->toOthers();
        } catch (\Throwable $exception) {
            Log::warning('Broadcast group chat mahasiswa gagal dikirim ke websocket.', [
                'message_id' => $message->id,
                'room_id' => $message->room_id,
                'error' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($message, $user, $room),
        ]);
    }

    public function update(Request $request, GroupChatMessage $message): JsonResponse
    {
        $validated = $request->validate([
            'pesan' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $room = $this->resolveRoomByOwnedMessage($user, $message);

        if (! $room || (int) $message->user_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan atau tidak bisa diedit.',
            ], 404);
        }

        // Edit grup tetap dibatasi ke pemilik pesan agar jejak diskusi tetap akuntabel.
        $message->update([
            'pesan' => trim($validated['pesan']),
        ]);

        $message->refresh()->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
        ]);

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($message, $user, $room),
        ]);
    }

    public function destroy(Request $request, GroupChatMessage $message): JsonResponse
    {
        $user = $request->user();
        $room = $this->resolveRoomByOwnedMessage($user, $message);

        if (! $room || (int) $message->user_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan atau tidak bisa dihapus.',
            ], 404);
        }

        // Pesan grup dihapus permanen hanya oleh pengirimnya sendiri.
        $message->delete();

        return response()->json([
            'success' => true,
            'deleted_id' => $message->id,
        ]);
    }

    private function joinPublicRoom(User $user, array $validated): array
    {
        $roomId = (int) ($validated['room_id'] ?? 0);
        $topic = $validated['topic'] ?? null;

        if (! $roomId && ! $topic) {
            throw ValidationException::withMessages([
                'topic' => 'Pilih grup publik yang tersedia terlebih dahulu.',
            ]);
        }

        if ($roomId) {
            $room = GroupChatRoom::query()
                ->whereKey($roomId)
                ->when(
                    GroupChatSupport::supportsRoomVisibility(),
                    fn ($query) => $query->where('visibility', GroupChatRoom::VISIBILITY_PUBLIC)
                )
                ->where('is_active', true)
                ->first();

            if (! $room) {
                throw ValidationException::withMessages([
                    'topic' => 'Grup publik yang dipilih tidak tersedia lagi.',
                ]);
            }
        } else {
            $topic = (string) $topic;
            $topicLabel = GroupChatRoom::topicOptions()[$topic];

            $room = GroupChatRoom::query()->firstOrCreate(
                ['topic' => $topic],
                array_filter([
                    'title' => 'Grup Konseling ' . $topicLabel,
                    'description' => GroupChatSupport::topicDescription($topic),
                    'visibility' => GroupChatSupport::supportsRoomVisibility() ? GroupChatRoom::VISIBILITY_PUBLIC : null,
                    'created_by' => $user->id,
                    'is_active' => true,
                ], fn ($value) => $value !== null)
            );
        }

        $topicLabel = $room->topicLabel();
        $member = $this->activateMembership($room, $user, 'public_topic');

        return [$room, $member, $topicLabel];
    }

    private function joinPrivateRoomByInvite(User $user, string $inviteToken): array
    {
        $room = GroupChatRoom::query()
            ->where('invite_token', $inviteToken)
            ->where('visibility', GroupChatRoom::VISIBILITY_PRIVATE)
            ->where('is_active', true)
            ->first();

        if (! $room) {
            throw ValidationException::withMessages([
                'invite_token' => 'Undangan grup privat tidak ditemukan atau sudah tidak aktif.',
            ]);
        }

        // Token undangan hanya valid untuk mahasiswa yang sudah terdaftar di daftar undangan grup tersebut.
        $this->resolvePrivateInvitationMembership($user, $room);
        $member = $this->activateMembership($room, $user, 'invite_link');

        return [$room, $member];
    }

    private function resolvePrivateInvitationMembership(User $user, GroupChatRoom $room): GroupChatMember
    {
        $member = GroupChatSupport::resolveRoomMember($user, $room);

        if (! $member) {
            throw ValidationException::withMessages([
                'invite_token' => 'Anda tidak terdaftar sebagai penerima undangan grup privat ini.',
            ]);
        }

        $this->guardMembershipReactivation($member);

        if (GroupChatSupport::supportsMembershipStatus() && ! in_array($member->membership_status, [
            GroupChatMember::STATUS_INVITED,
            GroupChatMember::STATUS_ACTIVE,
        ], true)) {
            throw ValidationException::withMessages([
                'invite_token' => 'Undangan grup privat ini sudah tidak dapat digunakan.',
            ]);
        }

        return $member;
    }

    private function activateMembership(GroupChatRoom $room, User $user, string $joinedVia): GroupChatMember
    {
        return DB::transaction(function () use ($room, $user, $joinedVia) {
            $member = GroupChatMember::query()->firstOrNew([
                'room_id' => $room->id,
                'user_id' => $user->id,
            ]);

            $this->guardMembershipReactivation($member);

            $fill = [];

            if (GroupChatSupport::supportsMembershipStatus()) {
                $fill['membership_status'] = GroupChatMember::STATUS_ACTIVE;
            }

            if (GroupChatSupport::supportsConsentTracking()) {
                $fill['consented_at'] = $this->nowInDisplayTimezone();
                $fill['consent_version'] = GroupChatSupport::consentVersion();
            }

            if (GroupChatSupport::supportsMembershipLifecycleFields()) {
                $fill['joined_via'] = $joinedVia;
                $fill['removed_at'] = null;
            }

            $member->fill($fill);

            if (! $member->joined_at) {
                $member->joined_at = $this->nowInDisplayTimezone();
            }

            if (GroupChatSupport::supportsMembershipLifecycleFields()) {
                if ($member->membership_status === GroupChatMember::STATUS_ACTIVE && filled($member->removed_reason)) {
                    $member->removed_reason = null;
                } elseif (! $member->exists || $member->getOriginal('membership_status') !== GroupChatMember::STATUS_ACTIVE) {
                    $member->removed_reason = null;
                }
            }

            $member->save();

            return GroupChatSupport::ensureMemberAlias($room, $member);
        });
    }

    private function guardMembershipReactivation(GroupChatMember $member): void
    {
        if (! $member->exists || ! GroupChatSupport::supportsMembershipStatus()) {
            return;
        }

        if ($member->membership_status === GroupChatMember::STATUS_REMOVED) {
            throw ValidationException::withMessages([
                'topic' => 'Anda sudah pernah dikeluarkan dari grup ini. Silakan hubungi konselor jika membutuhkan akses ulang.',
            ]);
        }

        if ($member->membership_status === GroupChatMember::STATUS_BLOCKED) {
            $message = $member->removed_reason === 'academic_inactive'
                ? 'Akses group chat Anda sedang nonaktif karena status akademik.'
                : 'Akses group chat Anda sedang diblokir oleh konselor.';

            throw ValidationException::withMessages([
                'topic' => $message,
            ]);
        }
    }

    private function resolveJoinedRooms(User $user)
    {
        $roomsQuery = GroupChatRoom::query()
            ->with([
                'latestMessage.sender.mahasiswa',
            ])
            ->where('is_active', true);

        if (GroupChatSupport::supportsMembershipStatus()) {
            $roomsQuery
                ->withCount([
                    'members as active_members_count' => fn ($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
                ])
                ->with([
                    'members' => fn ($query) => $query
                        ->where('membership_status', GroupChatMember::STATUS_ACTIVE)
                        ->with('user.mahasiswa'),
                ])
                ->whereHas('members', function ($query) use ($user) {
                    $query
                        ->where('user_id', $user->id)
                        ->where('membership_status', GroupChatMember::STATUS_ACTIVE);
                });
        } else {
            $roomsQuery
                ->withCount('members')
                ->with([
                    'members.user.mahasiswa',
                ])
                ->whereHas('members', fn ($query) => $query->where('user_id', $user->id));
        }

        $rooms = $roomsQuery->get()
            ->sortByDesc(function (GroupChatRoom $room) {
                return optional($room->latestMessage)->created_at?->getTimestamp()
                    ?? optional($room->updated_at)?->getTimestamp()
                    ?? 0;
            })
            ->values();

        foreach ($rooms as $room) {
            foreach ($room->members as $member) {
                GroupChatSupport::ensureMemberAlias($room, $member);
            }
        }

        return $rooms;
    }

    private function resolvePendingInvitations(User $user)
    {
        if (! GroupChatSupport::supportsPrivateGroups()) {
            return collect();
        }

        $invitations = GroupChatMember::query()
            ->with([
                'room',
                'inviter',
            ])
            ->where('user_id', $user->id)
            ->where('membership_status', GroupChatMember::STATUS_INVITED)
            ->whereHas('room', fn ($query) => $query->where('is_active', true))
            ->orderByDesc('updated_at')
            ->get();

        foreach ($invitations as $invitation) {
            if ($invitation->room) {
                GroupChatSupport::ensureMemberAlias($invitation->room, $invitation);
            }
        }

        return $invitations;
    }

    private function resolvePublicTopicCards(User $user): array
    {
        $joinedRoomIds = $this->resolveJoinedRooms($user)->pluck('id')->all();
        $publicRoomsQuery = GroupChatRoom::query()
            ->where('is_active', true);

        if (GroupChatSupport::supportsMembershipStatus()) {
            $publicRoomsQuery->withCount([
                'members as active_members_count' => fn ($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
            ]);
        } else {
            $publicRoomsQuery->withCount('members');
        }

        if (GroupChatSupport::supportsRoomVisibility()) {
            $publicRoomsQuery->where('visibility', GroupChatRoom::VISIBILITY_PUBLIC);
        }

        $publicRooms = $publicRoomsQuery->get()->keyBy('topic');

        $cards = [];

        foreach (GroupChatRoom::topicOptions() as $topicKey => $topicLabel) {
            /** @var GroupChatRoom|null $room */
            $room = $publicRooms->get($topicKey);

            $cards[] = [
                'topic_key' => $topicKey,
                'topic_label' => $topicLabel,
                'description' => GroupChatSupport::topicDescription($topicKey),
                'room_id' => $room?->id,
                'member_count' => (int) ($room?->active_members_count ?? $room?->members_count ?? 0),
                'joined' => $room ? in_array($room->id, $joinedRoomIds, true) : false,
                'join_url' => route('mahasiswa.group-chat.create', ['topic' => $topicKey]),
                'room_url' => $room ? route('mahasiswa.group-chat.room', ['group' => $room->id]) : null,
            ];
        }

        return $cards;
    }

    private function resolveAccessibleRoom(User $user, ?int $roomId): ?GroupChatRoom
    {
        if (! $roomId) {
            return null;
        }

        $roomQuery = GroupChatRoom::query()
            ->with([
                'messages.sender.mahasiswa',
            ])
            ->whereKey($roomId)
            ->where('is_active', true);

        if (GroupChatSupport::supportsMembershipStatus()) {
            $roomQuery
                ->withCount([
                    'members as active_members_count' => fn ($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
                ])
                ->with([
                    'members' => fn ($query) => $query
                        ->where('membership_status', GroupChatMember::STATUS_ACTIVE)
                        ->with('user.mahasiswa'),
                ])
                ->whereHas('members', function ($query) use ($user) {
                    $query
                        ->where('user_id', $user->id)
                        ->where('membership_status', GroupChatMember::STATUS_ACTIVE);
                });
        } else {
            $roomQuery
                ->withCount('members')
                ->with([
                    'members.user.mahasiswa',
                ])
                ->whereHas('members', fn ($query) => $query->where('user_id', $user->id));
        }

        $room = $roomQuery->first();

        if ($room) {
            foreach ($room->members as $member) {
                GroupChatSupport::ensureMemberAlias($room, $member);
            }
        }

        return $room;
    }

    private function buildChatPayload(GroupChatRoom $room, array $messages): array
    {
        return [
            'roomId' => $room->id,
            'channel' => 'chat.group.' . $room->id,
            'sendUrl' => route('mahasiswa.group-chat.store'),
            'messagesUrl' => route('mahasiswa.group-chat.messages'),
            'updateUrlTemplate' => route('mahasiswa.group-chat.update', ['message' => '__MESSAGE_ID__']),
            'deleteUrlTemplate' => route('mahasiswa.group-chat.destroy', ['message' => '__MESSAGE_ID__']),
            'roomTitle' => $room->title,
            'roomDescription' => $room->description,
            'topicLabel' => $room->topicLabel(),
            'visibilityLabel' => $room->visibilityLabel(),
            'memberCount' => (int) ($room->active_members_count ?? $room->members_count ?? $room->members->count()),
            'memberNames' => $this->resolveMemberNames($room),
            'memberProfiles' => $this->resolveMemberProfiles($room),
            'rules' => GroupChatSupport::rules(),
            'messages' => $messages,
        ];
    }

    private function buildPublicConsentContext(array $topicCard): array
    {
        if (! $user) {
            return 'Mahasiswa Anonim';
        }

        return $user->getAnonimDisplayName();
        return [
            'kind' => 'public_topic',
            'headline' => 'Konfirmasi gabung grup publik',
            'title' => 'Apakah Anda yakin ingin bergabung ke grup topik ' . $topicCard['topic_label'] . '?',
            'description' => $topicCard['description'],
            'meta' => $topicCard['member_count'] . ' anggota aktif',
            'submit_label' => 'Setuju dan Masuk Grup',
            'hidden_fields' => [
                'topic' => $topicCard['topic_key'],
                'room_id' => $topicCard['room_id'],
            ],
        ];
    }

    private function buildPrivateConsentContext(GroupChatRoom $room, ?GroupChatMember $membership): array
    {
        if (! $user) {
            return asset('img/default-avatar.png');
        }

        return $user->getAnonimAvatarSvg();
        $inviterName = $membership?->inviter?->nama ?: 'Konselor';
        $aliasName = $membership?->anonymous_name ?: 'Disiapkan otomatis';

        return [
            'kind' => 'private_invite',
            'headline' => 'Undangan grup privat',
            'title' => 'Anda diundang ke grup privat "' . $room->title . '".',
            'description' => $room->description ?: 'Grup privat ini dibuat oleh konselor untuk diskusi yang lebih terarah.',
            'meta' => 'Pengundang: ' . $inviterName . ' • Alias anonim Anda: ' . $aliasName,
            'submit_label' => 'Setuju dan Gabung Grup',
            'hidden_fields' => [
                'invite_token' => GroupChatSupport::supportsInviteToken() ? $room->invite_token : null,
            ],
        ];
    }

    private function resolveMemberNames(GroupChatRoom $room): array
    {
        return $room->members
            ->sortBy(fn (GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(function (GroupChatMember $member) use ($room) {
                return GroupChatSupport::resolveDisplayName($member->user, $room, $member);
            })
            ->filter()
            ->values()
            ->all();
    }

    private function resolveMemberProfiles(GroupChatRoom $room): array
    {
        return $room->members
            ->sortBy(fn (GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(function (GroupChatMember $member) use ($room) {
                return [
                    'name' => GroupChatSupport::resolveDisplayName($member->user, $room, $member),
                    'avatar_url' => GroupChatSupport::resolveAvatarUrl(),
                ];
            })
            ->filter(fn (array $member) => filled($member['name']))
            ->values()
            ->all();
    }

    private function transformMessage(GroupChatMessage $message, User $viewer, GroupChatRoom $room): array
    {
        $message->loadMissing([
            'sender.mahasiswa',
        ]);

        $sender = $message->sender;
        $membership = $sender ? GroupChatSupport::resolveRoomMember($sender, $room) : null;

        return [
            'id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->user_id,
            'sender_name' => $this->resolveUserDisplayName($sender),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $this->resolveUserAvatarUrl($sender),
            'sender_name' => $message->user_id === $viewer->id
                ? 'Anda'
                : GroupChatSupport::resolveDisplayName($sender, $room, $membership),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => GroupChatSupport::resolveAvatarUrl(),
            'text' => $message->pesan,
            'time' => $this->toDisplayDateTime($message->created_at)?->format('H:i') ?? $this->nowInDisplayTimezone()->format('H:i'),
            'sent_at' => $this->toDisplayDateTime($message->created_at)?->toIso8601String() ?? $this->nowInDisplayTimezone()->toIso8601String(),
            'updated_at' => $this->toDisplayDateTime($message->updated_at)?->toIso8601String(),
            'is_edited' => (bool) ($message->updated_at && $message->created_at && $message->updated_at->ne($message->created_at)),
            'is_mine' => $message->user_id === $viewer->id,
        ];
    }

    private function toDisplayDateTime($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return $value instanceof Carbon
            ? $value->copy()->timezone($this->displayTimezone())
            : Carbon::parse($value)->timezone($this->displayTimezone());
    }

    private function nowInDisplayTimezone(): Carbon
    {
        return Carbon::now($this->displayTimezone());
    }

    private function displayTimezone(): string
    {
        return 'Asia/Jakarta';
    }

    private function resolveRoomByOwnedMessage(User $user, GroupChatMessage $message): ?GroupChatRoom
    {
        $message->loadMissing([
            'room.members',
        ]);

        $room = $message->room;

        if (! $room || ! $room->is_active) {
            return null;
        }

        $isMember = $room->members->contains(function (GroupChatMember $member) use ($user) {
            if ((int) $member->user_id !== (int) $user->id) {
                return false;
            }

            if (! GroupChatSupport::supportsMembershipStatus()) {
                return true;
            }

            return $member->membership_status === GroupChatMember::STATUS_ACTIVE;
        });

        return $isMember ? $room : null;
    }
}

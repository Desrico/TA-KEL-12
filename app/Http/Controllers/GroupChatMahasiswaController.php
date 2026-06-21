<?php

namespace App\Http\Controllers;

use App\Events\GroupChatMessageSent;
use App\Models\GroupChatMember;
use App\Models\GroupChatMessage;
use App\Models\GroupChatRoom;
use App\Models\Notifikasi;
use App\Models\User;
use App\Support\GroupChatSupport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'topicOptions' => GroupChatRoom::topicOptions(),
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

        $this->markInviteNotificationsAsRead($user, $token);

        if ($membership && (! GroupChatSupport::supportsMembershipStatus() || $membership->membership_status === GroupChatMember::STATUS_ACTIVE)) {
            return redirect()
                ->route('mahasiswa.group-chat.room', ['group' => $room->id])
                ->with('success', 'Anda sudah tergabung di grup privat tersebut.');
        }

        return view('Pages.group-chat-create', [
            'topicOptions' => GroupChatRoom::topicOptions(),
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

        $messages = $this->buildVisibleMessages($activeRoom, $user);

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

                [$room, $member, $activatedNow] = $this->joinPrivateRoomByInvite($user, (string) $validated['invite_token']);

                if ($activatedNow) {
                    $this->createMembershipSystemMessage($room, $user, 'joined');
                }

                $this->markInviteNotificationsAsRead($user, (string) $validated['invite_token']);

                return redirect()
                    ->route('mahasiswa.group-chat.room', ['group' => $room->id])
                    ->with(
                        'success',
                        $member->wasRecentlyCreated || (GroupChatSupport::supportsMembershipStatus() && $member->getOriginal('membership_status') !== GroupChatMember::STATUS_ACTIVE)
                            ? 'Anda berhasil menerima undangan grup privat.'
                            : 'Ruang chat grup privat dibuka kembali.'
                    );
            }

            [$room, $member, $topicLabel, $activatedNow] = $this->joinPublicRoom($user, $validated);

            if ($activatedNow) {
                $this->createMembershipSystemMessage($room, $user, 'joined');
            }

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

    public function leave(Request $request, int $group): RedirectResponse
    {
        $user = $request->user();
        $room = $this->resolveAccessibleRoom($user, $group);

        if (! $room) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Grup chat tidak ditemukan atau Anda sudah keluar dari grup tersebut.');
        }

        $member = GroupChatSupport::resolveRoomMember($user, $room);

        if (! $member) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Keanggotaan grup tidak ditemukan.');
        }

        DB::transaction(function () use ($member) {
            if (GroupChatSupport::supportsMembershipStatus()) {
                $payload = [
                    'membership_status' => GroupChatMember::STATUS_LEFT,
                ];

                if (GroupChatSupport::supportsMembershipLifecycleFields()) {
                    $payload['removed_at'] = now();
                    $payload['removed_reason'] = 'left_by_member';
                }

                $member->update($payload);

                return;
            }

            $member->delete();
        });

        // Jejak sistem ini membantu anggota lain memahami kenapa nama tertentu tidak lagi muncul aktif di grup.
        $this->createMembershipSystemMessage($room, $user, 'left');

        return redirect()
            ->route('mahasiswa.group-chat')
            ->with('success', 'Anda telah keluar dari grup konseling.');
    }

    public function updateRoomAvatar(Request $request, int $group): RedirectResponse
    {
        $room = $this->resolveAccessibleRoom($request->user(), $group);

        if (! $room) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Grup chat tidak ditemukan.');
        }

        if (! GroupChatSupport::supportsRoomAvatar()) {
            return back()->with('error', 'Foto grup membutuhkan migration database terbaru sebelum dapat digunakan.');
        }

        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $oldPath = $room->avatar_path;
        $newPath = $validated['avatar']->store('group-chat/avatars', 'public');

        $room->update([
            'avatar_path' => $newPath,
        ]);

        if (filled($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()
            ->route('mahasiswa.group-chat.room', ['group' => $room->id])
            ->with('success', 'Foto grup berhasil diperbarui.');
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
            'messages' => $this->buildVisibleMessages($room, $request->user()),
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
            $payload = [
                'room_id' => $room->id,
                'user_id' => $user->id,
                'pesan' => trim($validated['pesan']),
            ];

            if (GroupChatSupport::supportsSystemMessages()) {
                $payload['is_system'] = false;
                $payload['system_event'] = null;
            }

            return GroupChatMessage::create($payload);
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

        if (! $room || (int) $message->user_id !== (int) $user->id || (bool) $message->is_system) {
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

        if (! $room || (int) $message->user_id !== (int) $user->id || (bool) $message->is_system) {
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
        [$member, $activatedNow] = $this->activateMembership($room, $user, 'public_topic');

        return [$room, $member, $topicLabel, $activatedNow];
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
        [$member, $activatedNow] = $this->activateMembership($room, $user, 'invite_link');

        return [$room, $member, $activatedNow];
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

    private function activateMembership(GroupChatRoom $room, User $user, string $joinedVia): array
    {
        return DB::transaction(function () use ($room, $user, $joinedVia) {
            $member = GroupChatMember::query()->firstOrNew([
                'room_id' => $room->id,
                'user_id' => $user->id,
            ]);

            $this->guardMembershipReactivation($member);
            $wasActiveBefore = $member->exists
                && (! GroupChatSupport::supportsMembershipStatus() || $member->getOriginal('membership_status') === GroupChatMember::STATUS_ACTIVE);

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

            return [
                GroupChatSupport::ensureMemberAlias($room, $member),
                ! $wasActiveBefore,
            ];
        });
    }

    private function markInviteNotificationsAsRead(User $user, string $inviteToken): void
    {
        $inviteToken = trim($inviteToken);

        if ($inviteToken === '') {
            return;
        }

        $inviteUrl = route('mahasiswa.group-chat.invite', ['token' => $inviteToken]);

        Notifikasi::query()
            ->where('user_id', $user->id)
            ->where('status', 'belum')
            ->where('cta_target', $inviteUrl)
            ->update(['status' => 'dibaca']);
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
        $sessionStartedAt = GroupChatSupport::roomUsesSessionReset($room)
            ? $this->toDisplayDateTime(GroupChatSupport::currentSessionStartedAt($room))
            : null;

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
            'roomAvatarUrl' => GroupChatSupport::resolveRoomAvatarUrl($room),
            'roomAvatarInitial' => GroupChatSupport::resolveRoomAvatarInitial($room),
            'rules' => GroupChatSupport::rules(),
            'canUpdateAvatar' => GroupChatSupport::supportsRoomAvatar(),
            'updateAvatarUrl' => route('mahasiswa.group-chat.room.avatar', ['group' => $room->id]),
            'sessionResetHours' => GroupChatSupport::roomUsesSessionReset($room)
                ? GroupChatSupport::sessionResetHours()
                : null,
            'sessionStartedAt' => $sessionStartedAt?->toIso8601String(),
            'messages' => $messages,
        ];
    }

    private function buildPublicConsentContext(array $topicCard): array
    {
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
        $inviterName = $membership?->inviter?->nama ?: 'Konselor';

        return [
            'kind' => 'private_invite',
            'room_title' => $room->title,
            'room_description' => $room->description,
            'meta' => 'Pengundang: ' . $inviterName . ' • Nama asli Anda akan tampil di grup privat ini.',
            'inviter_name' => $inviterName,
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

        $senderRole = strtolower((string) ($sender?->role ?? 'pengguna'));
        $isCounselorMessage = in_array($senderRole, ['konselor', 'admin'], true);

        return [
            'id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->user_id,
            'sender_name' => (bool) $message->is_system
                ? 'Sistem'
                : ($message->user_id === $viewer->id
                    ? 'Anda'
                    : ($isCounselorMessage ? 'Konselor' : GroupChatSupport::resolveDisplayName($sender, $room, $membership))),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => GroupChatSupport::resolveAvatarUrl(),
            'avatar_initial' => $isCounselorMessage ? 'K' : null,
            'is_counselor' => $isCounselorMessage,
            'text' => $message->pesan,
            'time' => $this->toDisplayDateTime($message->created_at)?->format('H:i') ?? $this->nowInDisplayTimezone()->format('H:i'),
            'sent_at' => $this->toDisplayDateTime($message->created_at)?->toIso8601String() ?? $this->nowInDisplayTimezone()->toIso8601String(),
            'updated_at' => $this->toDisplayDateTime($message->updated_at)?->toIso8601String(),
            'is_edited' => (bool) ($message->updated_at && $message->created_at && $message->updated_at->ne($message->created_at)),
            'is_system' => (bool) $message->is_system,
            'system_event' => $message->system_event,
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

    private function createMembershipSystemMessage(GroupChatRoom $room, User $user, string $event): GroupChatMessage
    {
        $member = GroupChatSupport::resolveRoomMember($user, $room);
        $displayName = GroupChatSupport::resolveDisplayName($user, $room, $member);
        $text = match ($event) {
            'joined' => $displayName . ' Bergabung',
            'left' => $displayName . ' Meninggalkan Grup',
            'removed' => $displayName . ' Dikeluarkan dari Grup',
            default => $displayName,
        };

        $payload = [
            'room_id' => $room->id,
            'user_id' => $user->id,
            'pesan' => $text,
        ];

        if (GroupChatSupport::supportsSystemMessages()) {
            $payload['is_system'] = true;
            $payload['system_event'] = $event;
        }

        $message = GroupChatMessage::create($payload);
        $message->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
            'room',
        ]);

        try {
            broadcast(new GroupChatMessageSent($message))->toOthers();
        } catch (\Throwable $exception) {
            Log::warning('Broadcast pesan sistem group chat mahasiswa gagal dikirim.', [
                'message_id' => $message->id,
                'room_id' => $message->room_id,
                'event' => $event,
                'error' => $exception->getMessage(),
            ]);
        }

        return $message;
    }

    private function buildVisibleMessages(GroupChatRoom $room, User $viewer): array
    {
        if (! GroupChatSupport::roomUsesSessionReset($room)) {
            return $room->messages
                ->sortBy('created_at')
                ->values()
                ->map(fn (GroupChatMessage $message) => $this->transformMessage($message, $viewer, $room))
                ->all();
        }

        $sessionStartedAt = GroupChatSupport::currentSessionStartedAt($room);
        $messages = $room->messages
            ->filter(fn (GroupChatMessage $message) => $message->created_at && $message->created_at->greaterThanOrEqualTo($sessionStartedAt))
            ->sortBy('created_at')
            ->values()
            ->map(fn (GroupChatMessage $message) => $this->transformMessage($message, $viewer, $room))
            ->all();

        array_unshift($messages, $this->buildSessionResetThread($room, $sessionStartedAt));

        return $messages;
    }

    private function buildSessionResetThread(GroupChatRoom $room, Carbon $sessionStartedAt): array
    {
        $displayTime = $this->toDisplayDateTime($sessionStartedAt) ?? $this->nowInDisplayTimezone();

        return [
            'id' => 'session-reset-' . $room->id . '-' . $displayTime->timestamp,
            'room_id' => $room->id,
            'sender_id' => null,
            'sender_name' => 'Sistem',
            'sender_role' => 'system',
            'avatar_url' => null,
            'avatar_initial' => null,
            'is_counselor' => false,
            'text' => 'Sesi grup baru dimulai. Riwayat percakapan disetel ulang setiap 1 minggu.',
            'time' => $displayTime->format('H:i'),
            'sent_at' => $displayTime->toIso8601String(),
            'updated_at' => null,
            'is_edited' => false,
            'is_system' => true,
            'system_event' => 'weekly_reset',
            'is_mine' => false,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\GroupChatMessageSent;
use App\Models\GroupChatMember;
use App\Models\GroupChatMessage;
use App\Models\GroupChatRoom;
use App\Models\User;
use App\Models\Notifikasi;
use App\Support\GroupChatSupport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;


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

        $publicTopics = $this->resolvePublicTopicCards($user);

        if ($topic && array_key_exists($topic, GroupChatRoom::topicOptions())) {
            $selectedTopic = collect($publicTopics)->firstWhere('topic_key', $topic);
        }

        return view('Pages.group-chat-create', [
            'publicTopics' => $publicTopics,
            'pendingInvitations' => $this->resolvePendingInvitations($user),
            'groupRules' => GroupChatSupport::rules(),
            'consentVersion' => GroupChatSupport::consentVersion(),
            'consentContext' => $selectedTopic ? $this->buildPublicConsentContext($selectedTopic) : null,
            'topicOptions' => GroupChatRoom::topicOptions(),
        ]);
    }

    private function buildPublicConsentContext(array $topic): array
    {
        return [
            'headline' => 'Persetujuan Grup Publik',
            'title' => 'Gabung ke grup “' . ($topic['topic_label'] ?? 'Grup Publik') . '”',
            'description' => 'Grup publik ini merupakan ruang percakapan anonim untuk mahasiswa dengan topik yang sama. Pastikan Anda setuju dengan aturan grup sebelum bergabung.',
            'group_name' => $topic['topic_label'] ?? 'Grup Publik',
            'inviter_name' => 'Sistem',
            'reason_label' => 'Alasan Bergabung',
            'invite_reason' => GroupChatSupport::topicDescription($topic['topic_key'] ?? null),
            'identity_visibility' => 'Nama Anda akan disamarkan saat berpartisipasi dalam grup publik.',
            'submit_label' => 'Setuju dan Gabung Grup',
            'hidden_fields' => [
                'topic' => $topic['topic_key'] ?? null,
                'room_id' => $topic['room_id'] ?? null,
            ],
        ];
    }

    private function buildPrivateConsentContext(GroupChatRoom $room, GroupChatMember $membership): array
    {
        $inviter = $membership->inviter;
        $inviterRole = strtolower((string) ($inviter?->role ?? ''));
        $inviterName = in_array($inviterRole, ['konselor', 'admin'], true)
            ? $this->resolveCisCounselorName($inviter)
            : ($inviter?->nama ?: $inviter?->name ?: 'Pengundang');

        return [
            'headline' => 'Persetujuan Undangan Grup Privat',
            'title' => 'Undangan untuk grup “' . $room->title . '”',
            'description' => 'Anda menerima undangan pribadi untuk bergabung ke grup privat ini. Bacalah aturan grup dengan seksama sebelum menyetujui undangan.',
            'group_name' => $room->title,
            'inviter_name' => $inviterName,
            'reason_label' => 'Alasan Diundang',
            'invite_reason' => $this->resolvePrivateInviteReason($room),
            'identity_visibility' => 'Nama Anda akan ditampilkan sesuai identitas asli di grup privat ini.',
            'submit_label' => 'Setuju dan Gabung Grup',
            'hidden_fields' => [
                'invite_token' => $room->invite_token,
            ],
        ];
    }

    private function resolvePrivateInviteReason(GroupChatRoom $room): string
    {
        $description = trim((string) ($room->description ?? ''));

        if ($description !== '') {
            return $description;
        }

        return 'Grup privat ini relevan untuk pendampingan dan diskusi konseling.';
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
                'members as active_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
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
            $membership = $this->resolvePrivateInvitationMembership($user, $room, $token);
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

        $messages = $activeRoom->messages()
            ->with([
                'sender.mahasiswa',
                'replyTo.sender',
            ])
            ->get()
            ->sortBy('created_at')
            ->values()
            ->map(fn(GroupChatMessage $message) => $this->transformMessage($message, $user, $activeRoom))
            ->all();

        return view('Pages.group-chat-room', [
            'joinedRooms' => $joinedRooms,
            'activeRoom' => $activeRoom,
            'chatPayload' => $this->buildChatPayload($activeRoom, $messages, $user),
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
                ->route('mahasiswa.group-chat.room', ['group' => $room->id]);
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
            'messages' => $room->messages()
                ->with([
                    'sender.mahasiswa',
                    'replyTo.sender',
                ])
                ->get()
                ->sortBy('created_at')
                ->values()
                ->map(fn(GroupChatMessage $message) => $this->transformMessage($message, $request->user(), $room))
                ->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'pesan' => 'required|string|max:2000',
            'reply_to_id' => 'nullable|integer',
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

        $replyToMessage = $this->resolveReplyMessageForRoom($room, $validated['reply_to_id'] ?? null);

        $message = DB::transaction(function () use ($room, $user, $validated, $replyToMessage) {
            return GroupChatMessage::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'pesan' => trim($validated['pesan']),
                'reply_to_message_id' => $replyToMessage?->id,
            ]);
        });

        $message->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
            'replyTo.sender',
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

    private function resolveReplyMessageForRoom(GroupChatRoom $room, mixed $replyToId): ?GroupChatMessage
    {
        $replyToId = (int) $replyToId;

        if ($replyToId <= 0) {
            return null;
        }

        return GroupChatMessage::query()
            ->where('room_id', $room->id)
            ->where(function ($query) {
                $query->whereNull('is_system')
                    ->orWhere('is_system', false);
            })
            ->find($replyToId);
    }

    public function leave(Request $request, GroupChatRoom $group): RedirectResponse
    {
        $user = $request->user();
        $room = $this->resolveAccessibleRoom($user, (int) $group->id);

        if (! $room) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Grup chat tidak ditemukan atau Anda belum menjadi anggota aktif.');
        }

        if ($room->isPrivate()) {
            return redirect()
                ->route('mahasiswa.group-chat.room', ['group' => $room->id])
                ->with('error', 'Keluar grup hanya tersedia untuk grup publik.');
        }

        $member = GroupChatSupport::resolveRoomMember($user, $room);

        if (! $member) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Keanggotaan grup tidak ditemukan.');
        }

        $leftMessage = $this->buildMembershipSystemText($room, $user, $member, 'left');

        DB::transaction(function () use ($member) {
            // Keluar grup publik tidak menghapus histori pesan, hanya menonaktifkan membership user.
            if (GroupChatSupport::supportsMembershipStatus()) {
                $member->membership_status = GroupChatMember::STATUS_LEFT;
            }

            if (GroupChatSupport::supportsMembershipLifecycleFields()) {
                $member->removed_at = $this->nowInDisplayTimezone();
                $member->removed_reason = 'left_by_member';
            }

            $member->save();
        });

        $this->createMembershipSystemMessage($room, $user, $leftMessage, 'left');

        return redirect()
            ->route('mahasiswa.group-chat')
            ->with('success', 'Anda berhasil keluar dari grup publik.')
            ->with('group_left_success_modal', [
                'title' => 'Berhasil Keluar dari Grup',
                'message' => 'Anda berhasil keluar dari grup publik. Grup tidak lagi tampil di daftar grup Anda.',
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
                    fn($query) => $query->where('visibility', GroupChatRoom::VISIBILITY_PUBLIC)
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
                ], fn($value) => $value !== null)
            );
        }

        $topicLabel = $room->topicLabel();
        $joinedNow = false;
        $member = $this->activateMembership($room, $user, 'public_topic', $joinedNow);

        if ($joinedNow) {
            $this->createMembershipSystemMessage(
                $room,
                $user,
                $this->buildMembershipSystemText($room, $user, $member, 'joined'),
                'joined'
            );
        }

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

        $this->resolvePrivateInvitationMembership($user, $room, $inviteToken);

        $joinedNow = false;
        $member = $this->activateMembership($room, $user, 'invite_link', $joinedNow);

        if ($joinedNow) {
            $this->createMembershipSystemMessage(
                $room,
                $user,
                $this->buildMembershipSystemText($room, $user, $member, 'joined'),
                'joined'
            );
        }

        $this->clearGroupInviteNotification($user, $room);

        return [$room, $member];
    }

    private function resolvePrivateInvitationMembership(User $user, GroupChatRoom $room, ?string $inviteToken = null): GroupChatMember
    {
        $member = GroupChatSupport::resolveRoomMember($user, $room);

        if (! $member) {
            // Fallback untuk undangan lama: jika notifikasi milik user memuat token grup ini,
            // buat membership invited sebelum consent mengaktifkan member.
            $member = $this->createInvitationMembershipFromNotification($user, $room, $inviteToken);

            if (! $member) {
                throw ValidationException::withMessages([
                    'invite_token' => 'Anda tidak terdaftar sebagai penerima undangan grup privat ini.',
                ]);
            }
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

    private function createInvitationMembershipFromNotification(User $user, GroupChatRoom $room, ?string $inviteToken): ?GroupChatMember
    {
        if (! filled($inviteToken) || ! Schema::hasColumn('notifikasi', 'cta_target')) {
            return null;
        }

        $hasInviteNotification = Notifikasi::query()
            ->where('user_id', $user->id)
            ->where(function ($query) use ($room, $inviteToken) {
                $query->where('cta_target', 'like', '%' . $inviteToken . '%')
                    ->orWhere('pesan', 'like', '%' . $room->title . '%');
            })
            ->exists();

        if (! $hasInviteNotification) {
            return null;
        }

        return DB::transaction(function () use ($user, $room) {
            $member = GroupChatMember::query()->firstOrNew([
                'room_id' => $room->id,
                'user_id' => $user->id,
            ]);

            $fill = [];

            if (GroupChatSupport::supportsMembershipStatus()) {
                $fill['membership_status'] = GroupChatMember::STATUS_INVITED;
            }

            if (GroupChatSupport::supportsMembershipLifecycleFields()) {
                $fill['joined_via'] = 'notification_invite';
                $fill['invited_by'] = $room->created_by;
                $fill['removed_at'] = null;
                $fill['removed_reason'] = null;
            }

            $member->fill($fill);
            $member->save();

            return GroupChatSupport::ensureMemberAlias($room, $member);
        });
    }

    private function activateMembership(GroupChatRoom $room, User $user, string $joinedVia, ?bool &$joinedNow = null): GroupChatMember
    {
        return DB::transaction(function () use ($room, $user, $joinedVia, &$joinedNow) {
            $member = GroupChatMember::query()->firstOrNew([
                'room_id' => $room->id,
                'user_id' => $user->id,
            ]);

            $this->guardMembershipReactivation($member);
            $previousStatus = $member->exists ? $member->membership_status : null;
            $joinedNow = GroupChatSupport::supportsMembershipStatus()
                ? (! $member->exists || $previousStatus !== GroupChatMember::STATUS_ACTIVE)
                : ! $member->exists;

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
                    'members as active_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
                ])
                ->with([
                    'members' => fn($query) => $query
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
                ->whereHas('members', fn($query) => $query->where('user_id', $user->id));
        }

        $rooms = $roomsQuery->get()
            ->sort(function (GroupChatRoom $firstRoom, GroupChatRoom $secondRoom) {
                $firstPrivateRank = $firstRoom->isPrivate() ? 1 : 0;
                $secondPrivateRank = $secondRoom->isPrivate() ? 1 : 0;

                if ($firstPrivateRank !== $secondPrivateRank) {
                    return $secondPrivateRank <=> $firstPrivateRank;
                }

                $firstActivity = optional($firstRoom->latestMessage)->created_at?->getTimestamp()
                    ?? optional($firstRoom->updated_at)?->getTimestamp()
                    ?? 0;

                $secondActivity = optional($secondRoom->latestMessage)->created_at?->getTimestamp()
                    ?? optional($secondRoom->updated_at)?->getTimestamp()
                    ?? 0;

                return $secondActivity <=> $firstActivity;
            })
            ->values();

        foreach ($rooms as $room) {
            if (! $room->isPrivate()) {
                foreach ($room->members as $member) {
                    GroupChatSupport::ensureMemberAlias($room, $member);
                }
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
            ->whereHas('room', fn($query) => $query->where('is_active', true))
            ->orderByDesc('updated_at')
            ->get();

        foreach ($invitations as $invitation) {
            if ($invitation->room && ! $invitation->room->isPrivate()) {
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
                'members as active_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
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
                    'members as active_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
                ])
                ->with([
                    'members' => fn($query) => $query
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
                ->whereHas('members', fn($query) => $query->where('user_id', $user->id));
        }

        $room = $roomQuery->first();

        if ($room && ! $room->isPrivate()) {
            foreach ($room->members as $member) {
                GroupChatSupport::ensureMemberAlias($room, $member);
            }
        }

        return $room;
    }

    private function buildChatPayload(GroupChatRoom $room, array $messages, ?User $viewer = null): array
    {
        return [
            'roomId' => $room->id,
            'isPrivate' => $room->isPrivate(),
            'currentMemberName' => $viewer
                ? $this->resolveRoomDisplayName(
                    $viewer,
                    $room,
                    GroupChatSupport::resolveRoomMember($viewer, $room)
                )
                : null,
            'channel' => 'chat.group.' . $room->id,
            'sendUrl' => route('mahasiswa.group-chat.store'),
            'messagesUrl' => route('mahasiswa.group-chat.messages'),
            'updateUrlTemplate' => route('mahasiswa.group-chat.update', ['message' => '__MESSAGE_ID__']),
            'deleteUrlTemplate' => route('mahasiswa.group-chat.destroy', ['message' => '__MESSAGE_ID__']),
            'roomTitle' => $room->title,
            'roomDescription' => $room->description,
            'counselorName' => $this->resolveRoomCounselorName($room, $messages),
            'topicLabel' => $room->topicLabel(),
            'visibilityLabel' => $room->visibilityLabel(),
            'memberCount' => (int) ($room->active_members_count ?? $room->members_count ?? $room->members->count()),
            'memberNames' => $this->resolveMemberNames($room),
            'memberProfiles' => $this->resolveMemberProfiles($room),
            'canLeave' => ! $room->isPrivate(),
            'leaveUrl' => ! $room->isPrivate() ? route('mahasiswa.group-chat.leave', ['group' => $room->id]) : null,
            'rules' => GroupChatSupport::rules(),
            'messages' => $messages,
        ];
    }

    private function resolveRoomDisplayName(?User $user, GroupChatRoom $room, ?GroupChatMember $membership = null): string
    {
        if (! $user) {
            return 'Mahasiswa';
        }

        $role = strtolower((string) ($user->role ?? ''));

        if (in_array($role, ['konselor', 'admin'], true)) {
            return $this->resolveCisCounselorName($user);
        }

        if ($room->isPrivate()) {
            return trim((string) (
                $user->nama
                ?: $user->name
                ?: $user->username_cis
                ?: $user->email
                ?: 'Mahasiswa'
            ));
        }

        return GroupChatSupport::resolveDisplayName($user, $room, $membership);
    }

    private function resolveCisCounselorName(?User $user = null): string
    {
        return trim((string) (
            $user?->nama
                ?: $user?->name
                ?: $user?->username_cis
                ?: $user?->email
                ?: env('CIS_KONSELOR_NAME', 'Konselor')
        )) ?: 'Konselor';
    }

    private function resolveRoomCounselorName(GroupChatRoom $room, array $messages = []): string
    {
        $counselor = $room->members
            ->map(fn (GroupChatMember $member) => $member->user)
            ->first(function (?User $user) {
                $role = strtolower((string) ($user?->role ?? ''));

                return in_array($role, ['konselor', 'admin'], true);
            });

        $memberName = $this->resolveCisCounselorName($counselor);

        if (strtolower($memberName) !== 'konselor') {
            return $memberName;
        }

        foreach ($messages as $message) {
            $isCounselorMessage = (bool) ($message['is_counselor'] ?? false)
                || in_array(strtolower((string) ($message['sender_role'] ?? '')), ['konselor', 'admin'], true);
            $messageSenderName = trim((string) ($message['sender_name'] ?? ''));

            if ($isCounselorMessage && $messageSenderName !== '' && strtolower($messageSenderName) !== 'konselor') {
                return $messageSenderName;
            }
        }

        return $memberName;
    }

    private function resolveRoomAvatarUrl(?User $user, GroupChatRoom $room, ?GroupChatMember $membership = null): string
    {
        if ($room->isPrivate()) {
            $role = strtolower((string) ($user?->role ?? ''));
            $name = trim((string) (
                in_array($role, ['konselor', 'admin'], true)
                    ? $this->resolveCisCounselorName($user)
                    : ($user?->nama
                ?: $user?->name
                ?: $user?->username_cis
                ?: $user?->email
                ?: 'Mahasiswa')
            ));

            return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=d9f7e7&color=065f46';
        }

        return GroupChatSupport::resolveAvatarUrl();
    }

    private function resolveMemberNames(GroupChatRoom $room): array
    {
        return $room->members
            ->sortBy(fn(GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(function (GroupChatMember $member) use ($room) {
                return $this->resolveRoomDisplayName($member->user, $room, $member);
            })
            ->filter()
            ->values()
            ->all();
    }

    private function resolveMemberProfiles(GroupChatRoom $room): array
    {
        return $room->members
            ->sortBy(fn(GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(function (GroupChatMember $member) use ($room) {
                return [
                    'name' => $this->resolveRoomDisplayName($member->user, $room, $member),
                    'avatar_url' => $this->resolveRoomAvatarUrl($member->user, $room, $member),
                    'role' => $member->user?->role,
                    'is_private' => $room->isPrivate(),
                ];
            })
            ->filter(fn(array $member) => filled($member['name']))
            ->values()
            ->all();
    }

    private function transformMessage(GroupChatMessage $message, User $viewer, GroupChatRoom $room): array
    {
        $message->loadMissing([
            'sender',
            'sender.mahasiswa',
            'replyTo.sender',
        ]);

        $sender = $message->sender;
        $membership = $sender ? GroupChatSupport::resolveRoomMember($sender, $room) : null;
        $replyTo = $message->replyTo;
        $replySender = $replyTo?->sender;
        $replyMembership = $replySender ? GroupChatSupport::resolveRoomMember($replySender, $room) : null;

        $senderRole = strtolower((string) ($sender?->role ?? 'pengguna'));
        $isCounselorSender = in_array($senderRole, ['konselor', 'admin'], true);
        $replySenderRole = strtolower((string) ($replySender?->role ?? 'pengguna'));
        $isReplyCounselorSender = in_array($replySenderRole, ['konselor', 'admin'], true);

        $senderDisplayName = $isCounselorSender
            ? $this->resolveCisCounselorName($sender)
            : ($sender
                ? $this->resolveRoomDisplayName($sender, $room, $membership)
                : 'Mahasiswa');

        $messageText = (bool) $message->is_system
            ? match ($message->system_event) {
                'joined' => $senderDisplayName . ' telah bergabung ke Grup',
                'left' => $senderDisplayName . ' telah meninggalkan Grup',
                'removed' => $senderDisplayName . ' telah dikeluarkan dari Grup',
                default => $message->pesan,
            }
            : $message->pesan;

        return [
            'id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->user_id,
            'sender_name' => $senderDisplayName,
            'sender_anonymous_name' => $senderDisplayName,
            'sender_role' => $sender?->role ?? 'pengguna',
            'is_counselor' => $isCounselorSender,
            'avatar_url' => $this->resolveRoomAvatarUrl($sender, $room, $membership),
            'text' => $messageText,
            'reply_to' => $replyTo ? [
                'id' => $replyTo->id,
                'sender_id' => $replyTo->user_id,
                'sender_name' => (int) $replyTo->user_id === (int) $viewer->id
                    ? 'Anda'
                    : ($isReplyCounselorSender
                        ? $this->resolveCisCounselorName($replySender)
                        : ($replySender
                            ? $this->resolveRoomDisplayName($replySender, $room, $replyMembership)
                            : 'Pengguna')),
                'text' => $replyTo->pesan,
            ] : null,
            'time' => $this->toDisplayDateTime($message->created_at)?->format('H:i') ?? $this->nowInDisplayTimezone()->format('H:i'),
            'sent_at' => $this->toDisplayDateTime($message->created_at)?->toIso8601String() ?? $this->nowInDisplayTimezone()->toIso8601String(),
            'updated_at' => $this->toDisplayDateTime($message->updated_at)?->toIso8601String(),
            'is_edited' => (bool) ($message->updated_at && $message->created_at && $message->updated_at->ne($message->created_at)),
            'is_mine' => $message->user_id === $viewer->id,
            'is_private_room' => $room->isPrivate(),
            'is_system' => (bool) $message->is_system,
            'system_event' => $message->system_event,
        ];
    }

    private function buildMembershipSystemText(GroupChatRoom $room, User $user, ?GroupChatMember $member, string $event): string
    {
        $displayName = $this->resolveRoomDisplayName($user, $room, $member);

        return match ($event) {
            'joined' => $displayName . ' telah bergabung ke Grup',
            'left' => $displayName . ' telah meninggalkan Grup',
            'removed' => $displayName . ' telah dikeluarkan dari Grup',
            default => $displayName,
        };
    }

    private function createMembershipSystemMessage(GroupChatRoom $room, User $user, string $text, string $event): GroupChatMessage
    {
        $payload = [
            'room_id' => $room->id,
            'user_id' => $user->id,
            'pesan' => $text,
        ];

        if (GroupChatSupport::supportsSystemMessages()) {
            $payload['is_system'] = true;
            $payload['system_event'] = $event;
        }

        return GroupChatMessage::create($payload);
    }

    private function clearGroupInviteNotification(User $user, GroupChatRoom $room): void
    {
        $notifications = Notifikasi::where('user_id', $user->id)
            ->where(function ($q) use ($room) {
                $q->where('pesan', 'like', '%' . $room->title . '%');

                if (! empty($room->invite_token)) {
                    $q->orWhere('cta_target', 'like', '%' . $room->invite_token . '%');
                }
            })
            ->get();

        // Setelah consent disetujui sekali, notifikasi lama menjadi link biasa ke grup terkait.
        $updateData = [
            'pesan' => 'Anda telah diundang ke grup "' . $room->title . '".',
        ];

        if (Schema::hasColumn('notifikasi', 'cta_label')) {
            $updateData['cta_label'] = null;
        }

        if (Schema::hasColumn('notifikasi', 'cta_target')) {
            $updateData['cta_target'] = route('mahasiswa.group-chat.room', $room->id);
        }

        if ($notifications->isEmpty()) {
            return;
        }

        foreach ($notifications as $notification) {
            $notification->update($updateData);
        }
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

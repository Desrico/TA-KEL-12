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

class GroupChatMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($request->filled('group')) {
            return redirect()->route('mahasiswa.group-chat.room', ['group' => $request->integer('group')]);
        }

        // Halaman lobby mahasiswa hanya menampilkan grup yang sudah pernah diikuti.
        $joinedRooms = $this->resolveJoinedRooms($user);

        return view('Pages.group-chat', [
            'joinedRooms' => $joinedRooms,
            'consentPayload' => session('groupChatConsent'),
        ]);
    }

    public function create()
    {
        // Halaman terpisah untuk membuat grup baru berdasarkan topik konseling.
        return view('Pages.group-chat-create', [
            'topicOptions' => GroupChatRoom::topicOptions(),
            'consentPayload' => session('groupChatConsent'),
        ]);
    }

    public function consent(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['nullable', 'integer'],
            'topic' => ['nullable', 'string', Rule::in(array_keys(GroupChatRoom::topicOptions()))],
        ]);

        $user = $request->user();
        $roomId = (int) ($validated['room_id'] ?? 0);
        $topic = $validated['topic'] ?? null;

        if (! $roomId && ! $topic) {
            return redirect()
                ->route('mahasiswa.group-chat.create')
                ->with('error', 'Pilih topik atau grup terlebih dahulu sebelum melanjutkan.');
        }

        $room = null;

        if ($roomId) {
            $room = GroupChatRoom::query()
                ->whereKey($roomId)
                ->where('is_active', true)
                ->first();

            if (! $room) {
                return redirect()
                    ->route('mahasiswa.group-chat.create')
                    ->with('error', 'Grup yang dipilih tidak tersedia lagi.');
            }

            $topic = $room->topic;
        } elseif ($topic) {
            $room = GroupChatRoom::query()
                ->where('topic', $topic)
                ->where('is_active', true)
                ->first();
        }

        if ($room && $user) {
            $member = GroupChatSupport::resolveRoomMember($user, $room);

            if ($member && $member->isActive() && $this->memberHasCurrentConsent($member)) {
                return redirect()->route('mahasiswa.group-chat.room', ['group' => $room->id]);
            }
        }

        $payload = $this->buildConsentPayload(
            room: $room,
            topic: (string) $topic,
            token: null,
            cancelUrl: $roomId ? route('mahasiswa.group-chat') : route('mahasiswa.group-chat.create'),
            mode: 'public',
        );

        return redirect()
            ->route($roomId ? 'mahasiswa.group-chat' : 'mahasiswa.group-chat.create')
            ->with('groupChatConsent', $payload);
    }

    public function room(Request $request, int $group)
    {
        $user = $request->user();
        $joinedRooms = $this->resolveJoinedRooms($user);
        $activeRoom = $this->resolveAccessibleRoom($user, $group);

        if (! $activeRoom) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Anda belum tergabung di grup chat tersebut.');
        }

        $member = GroupChatSupport::resolveRoomMember($user, $activeRoom);

        if ($member && ! $this->memberHasCurrentConsent($member)) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('groupChatConsent', $this->buildConsentPayload(
                    room: $activeRoom,
                    topic: (string) $activeRoom->topic,
                    token: null,
                    cancelUrl: route('mahasiswa.group-chat'),
                    mode: $activeRoom->isPrivate() ? 'private' : 'public',
                ));
        }

        $messages = $activeRoom->messages
            ->sortBy('created_at')
            ->values()
            ->map(fn(GroupChatMessage $message) => $this->transformMessage($message, $user))
            ->all();

        return view('Pages.group-chat-room', [
            'joinedRooms' => $joinedRooms,
            'activeRoom' => $activeRoom,
            'chatPayload' => $this->buildChatPayload($activeRoom, $messages),
        ]);
    }

    public function invitation(Request $request, string $token)
    {
        $user = $request->user();
        $invitation = $this->resolveInvitationAccess($user, $token);

        if ($invitation instanceof RedirectResponse) {
            return $invitation;
        }

        ['room' => $room, 'member' => $member] = $invitation;

        if ($member->isActive() && $this->memberHasCurrentConsent($member)) {
            return redirect()
                ->route('mahasiswa.group-chat.room', ['group' => $room->id])
                ->with('success', 'Undangan grup privat "' . $room->title . '" berhasil dibuka.');
        }

        return redirect()
            ->route('mahasiswa.group-chat')
            ->with('groupChatConsent', $this->buildConsentPayload(
                room: $room,
                topic: (string) $room->topic,
                token: $token,
                cancelUrl: route('mahasiswa.group-chat'),
                mode: 'private',
            ));
    }

    public function join(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['nullable', 'string'],
            'room_id' => ['nullable', 'integer'],
            'topic' => ['nullable', 'string'],
            'consent_confirmed' => ['accepted'],
        ], [
            'consent_confirmed.accepted' => 'Centang persetujuan aturan grup terlebih dahulu sebelum melanjutkan.',
        ]);

        $user = $request->user();
        $token = $validated['token'] ?? null;
        $roomId = (int) ($validated['room_id'] ?? 0);
        $topic = $validated['topic'] ?? null;
        $topicOptions = GroupChatRoom::topicOptions();

        if (! $token && ! $roomId && ! $topic) {
            return back()
                ->withErrors(['topic' => 'Pilih grup yang tersedia atau tentukan topik baru terlebih dahulu.'])
                ->withInput();
        }

        if (! $token && ! $roomId && $topic && ! array_key_exists($topic, $topicOptions)) {
            return back()
                ->withErrors(['topic' => 'Topik grup yang dipilih tidak valid.'])
                ->withInput();
        }

        if ($token) {
            $invitation = $this->resolveInvitationAccess($user, $token);

            if ($invitation instanceof RedirectResponse) {
                return $invitation;
            }

            ['room' => $room, 'member' => $member] = $invitation;
            $shouldCreateJoinMessage = $this->shouldCreateJoinSystemMessage($member);

            $member = DB::transaction(function () use ($member, $room) {
                $member = $this->activateMembership($member, [
                    'joined_via' => 'invite_link',
                    'mark_consented' => true,
                ]);

                if (GroupChatSupport::supportsAnonymousName()) {
                    $member = GroupChatSupport::ensureMemberAlias($room, $member);
                }

                return $member;
            });

            if ($shouldCreateJoinMessage) {
                $this->createMembershipSystemMessage($room, $user, 'joined');
            }

            return redirect()
                ->route('mahasiswa.group-chat.room', ['group' => $room->id])
                ->with(
                    'success',
                    $member->wasRecentlyCreated
                        ? 'Anda berhasil masuk ke grup privat "' . $room->title . '".'
                        : 'Anda berhasil bergabung ke grup privat "' . $room->title . '".'
                );
        }

        if ($roomId) {
            // room_id dipakai saat mahasiswa masuk ke grup yang sudah tersedia.
            $room = GroupChatRoom::query()
                ->whereKey($roomId)
                ->where('is_active', true)
                ->first();

            if (! $room) {
                return back()
                    ->with('error', 'Grup yang dipilih tidak tersedia lagi.')
                    ->withInput();
            }

            $topicLabel = $room->topicLabel();
            $shouldCreateJoinMessage = false;

            [$member, $shouldCreateJoinMessage] = DB::transaction(function () use ($room, $user) {
                $member = GroupChatMember::query()->firstOrCreate(
                    [
                        'room_id' => $room->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'joined_at' => $this->nowInDisplayTimezone(),
                    ]
                );

                $shouldCreateJoinMessage = $this->shouldCreateJoinSystemMessage($member);

                return [$this->activateMembership($member, [
                    'joined_via' => 'topic_selection',
                    'mark_consented' => true,
                ]), $shouldCreateJoinMessage];
            });

            if ($shouldCreateJoinMessage) {
                $this->createMembershipSystemMessage($room, $user, 'joined');
            }
        } else {
            // Jika topik belum punya room aktif, sistem membuat room baru lalu langsung join.
            $topic = (string) $topic;
            $topicLabel = $topicOptions[$topic];
            $shouldCreateJoinMessage = false;

            [$room, $member, $shouldCreateJoinMessage] = DB::transaction(function () use ($topic, $topicLabel, $user) {
                $room = GroupChatRoom::query()->firstOrCreate(
                    ['topic' => $topic],
                    [
                        'title' => 'Grup Konseling ' . $topicLabel,
                        'description' => 'Ruang diskusi bersama untuk topik ' . $topicLabel . '.',
                        'created_by' => $user->id,
                        'is_active' => true,
                    ]
                );

                $member = GroupChatMember::query()->firstOrCreate(
                    [
                        'room_id' => $room->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'joined_at' => $this->nowInDisplayTimezone(),
                    ]
                );

                $shouldCreateJoinMessage = $this->shouldCreateJoinSystemMessage($member);

                return [$room, $this->activateMembership($member, [
                    'joined_via' => 'topic_selection',
                    'mark_consented' => true,
                ]), $shouldCreateJoinMessage];
            });

            if ($shouldCreateJoinMessage) {
                $this->createMembershipSystemMessage($room, $user, 'joined');
            }
        }

        return redirect()
            ->route('mahasiswa.group-chat.room', ['group' => $room->id])
            ->with(
                'success',
                $member->wasRecentlyCreated
                    ? 'Anda berhasil masuk ke grup konseling ' . $topicLabel . '.'
                    : 'Ruang chat grup konseling ' . $topicLabel . ' dibuka kembali.'
            );
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

        $this->createMembershipSystemMessage($room, $user, 'left');

        return redirect()
            ->route('mahasiswa.group-chat')
            ->with('success', 'Anda telah keluar dari grup konseling.');
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
                ->map(fn(GroupChatMessage $message) => $this->transformMessage($message, $request->user()))
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
            'message' => $this->transformMessage($message, $user),
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

        if ($message->isSystemMessage()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan sistem tidak dapat diedit.',
            ], 422);
        }

        // Edit grup juga dibatasi ke pengirim pesan itu sendiri.
        $message->update([
            'pesan' => trim($validated['pesan']),
        ]);

        $message->refresh()->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
        ]);

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($message, $user),
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

        if ($message->isSystemMessage()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan sistem tidak dapat dihapus.',
            ], 422);
        }

        // Hapus permanen berlaku untuk pesan milik sendiri agar konsisten di semua ruang chat.
        $message->delete();

        return response()->json([
            'success' => true,
            'deleted_id' => $message->id,
        ]);
    }

    private function resolveJoinedRooms(User $user)
    {
        return GroupChatRoom::query()
            ->withCount([
                'members as members_count' => fn($query) => $this->scopeVisibleMembersQuery($query),
            ])
            ->with([
                'members.user.profil',
                'latestMessage.sender.profil',
                'latestMessage.sender.mahasiswa',
            ])
            ->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
                $this->scopeVisibleMembersQuery($query);
            })
            ->where('is_active', true)
            ->get()
            ->sortByDesc(function (GroupChatRoom $room) {
                return optional($room->latestMessage)->created_at?->getTimestamp()
                    ?? optional($room->updated_at)?->getTimestamp()
                    ?? 0;
            })
            ->values();
    }

    private function resolveAccessibleRoom(User $user, ?int $roomId): ?GroupChatRoom
    {
        if (! $roomId) {
            return null;
        }

        return GroupChatRoom::query()
            ->with([
                'messages.sender.profil',
                'messages.sender.mahasiswa',
                'members.user.profil',
                'members.user.mahasiswa',
            ])
            ->withCount([
                'members as members_count' => fn($query) => $this->scopeVisibleMembersQuery($query),
            ])
            ->whereKey($roomId)
            ->where('is_active', true)
            ->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
                $this->scopeVisibleMembersQuery($query);
            })
            ->first();
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
            'leaveUrl' => route('mahasiswa.group-chat.leave', ['group' => $room->id]),
            'roomTitle' => $room->title,
            'topicLabel' => $room->topicLabel(),
            'memberCount' => (int) ($room->members_count ?? $room->members->count()),
            'memberNames' => $this->resolveMemberNames($room),
            'memberProfiles' => $this->resolveMemberProfiles($room),
            'messages' => $messages,
        ];
    }

    private function resolveUserDisplayName(?User $user): string
    {
        if (! $user) {
            return 'Mahasiswa Anonim';
        }

        return $user->getAnonimDisplayName();
    }

    private function resolveUserAvatarUrl(?User $user): string
    {
        if (! $user) {
            return asset('img/default-avatar.png');
        }

        return $user->getAnonimAvatarSvg();
    }

    private function resolveMemberNames(GroupChatRoom $room): array
    {
        $members = $room->members;
        $members = $this->filterVisibleMembers($members);

        $memberNames = $members
            ->sortBy(fn(GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(fn(GroupChatMember $member) => $this->resolveUserDisplayName($member->user))
            ->filter()
            ->values();

        if ($memberNames->isNotEmpty() || (int) ($room->members_count ?? 0) === 0) {
            return $memberNames->all();
        }

        // Fallback langsung dari tabel anggota menjaga dropdown tetap terisi jika relasi belum stabil.
        return GroupChatMember::query()
            ->with('user.mahasiswa')
            ->where('room_id', $room->id)
            ->tap(fn($query) => $this->scopeVisibleMembersQuery($query))
            ->get()
            ->sortBy(fn(GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(fn(GroupChatMember $member) => $this->resolveUserDisplayName($member->user))
            ->filter()
            ->values()
            ->all();
    }

    private function resolveMemberProfiles(GroupChatRoom $room): array
    {
        $members = $room->members;
        $members = $this->filterVisibleMembers($members);

        if ($members->isEmpty() && (int) ($room->members_count ?? 0) > 0) {
            // Fallback langsung dari tabel anggota menjaga foto dan nama tetap tersedia saat relasi belum stabil.
            $members = GroupChatMember::query()
                ->with(['user.mahasiswa', 'user.profil'])
                ->where('room_id', $room->id)
                ->tap(fn($query) => $this->scopeVisibleMembersQuery($query))
                ->get();
        }

        return $members
            ->sortBy(fn(GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(fn(GroupChatMember $member) => [
                'name' => $this->resolveUserDisplayName($member->user),
                'avatar_url' => $this->resolveUserAvatarUrl($member->user),
            ])
            ->filter(fn(array $member) => filled($member['name']))
            ->values()
            ->all();
    }

    private function transformMessage(GroupChatMessage $message, User $viewer): array
    {
        $message->loadMissing([
            'sender',
        ]);

        $sender = $message->sender;

        return [
            'id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->user_id,
            'sender_name' => $this->resolveUserDisplayName($sender),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $this->resolveUserAvatarUrl($sender),
            'text' => $message->pesan,
            'is_system' => $message->isSystemMessage(),
            'system_event' => $message->system_event,
            'time' => $this->toDisplayDateTime($message->created_at)?->format('H:i') ?? $this->nowInDisplayTimezone()->format('H:i'),
            'sent_at' => $this->toDisplayDateTime($message->created_at)?->toIso8601String() ?? $this->nowInDisplayTimezone()->toIso8601String(),
            'updated_at' => $this->toDisplayDateTime($message->updated_at)?->toIso8601String(),
            'is_edited' => (bool) ($message->updated_at && $message->created_at && $message->updated_at->ne($message->created_at)),
            'is_mine' => ! $message->isSystemMessage() && $message->user_id === $viewer->id,
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

        $isMember = $this->filterVisibleMembers($room->members)
            ->contains(fn(GroupChatMember $member) => (int) $member->user_id === (int) $user->id);

        return $isMember ? $room : null;
    }

    private function activateMembership(GroupChatMember $member, array $options = []): GroupChatMember
    {
        if (! GroupChatSupport::supportsMembershipStatus()) {
            return $member;
        }

        $joinedVia = $options['joined_via'] ?? null;
        $markConsented = (bool) ($options['mark_consented'] ?? false);
        $wasRecentlyCreated = $member->wasRecentlyCreated;
        $payload = [];

        if ($member->membership_status !== GroupChatMember::STATUS_ACTIVE) {
            $payload['membership_status'] = GroupChatMember::STATUS_ACTIVE;
        }

        if (! $member->joined_at) {
            $payload['joined_at'] = $this->nowInDisplayTimezone();
        }

        if (GroupChatSupport::supportsMembershipLifecycleFields()) {
            if ($joinedVia) {
                $payload['joined_via'] = $joinedVia;
            }
            $payload['removed_at'] = null;
            $payload['removed_reason'] = null;
        }

        if ($markConsented && GroupChatSupport::supportsConsentTracking()) {
            $payload['consented_at'] = $this->nowInDisplayTimezone();
            $payload['consent_version'] = GroupChatSupport::consentVersion();
        }

        if ($payload === []) {
            return $member;
        }

        $member->update($payload);
        $member = $member->refresh();
        $member->wasRecentlyCreated = $wasRecentlyCreated;

        return $member;
    }

    private function shouldCreateJoinSystemMessage(GroupChatMember $member): bool
    {
        if ($member->wasRecentlyCreated) {
            return true;
        }

        if (! GroupChatSupport::supportsMembershipStatus()) {
            return false;
        }

        return $member->membership_status !== GroupChatMember::STATUS_ACTIVE;
    }

    private function createMembershipSystemMessage(GroupChatRoom $room, User $user, string $event): GroupChatMessage
    {
        $text = match ($event) {
            'joined' => $this->resolveUserDisplayName($user) . ' Bergabung',
            'left' => $this->resolveUserDisplayName($user) . ' Meninggalkan Grup',
            default => $this->resolveUserDisplayName($user),
        };

        $message = GroupChatMessage::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'is_system' => true,
            'system_event' => $event,
            'pesan' => $text,
        ]);

        $message->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
            'room',
        ]);

        try {
            broadcast(new GroupChatMessageSent($message))->toOthers();
        } catch (\Throwable $exception) {
            Log::warning('Broadcast system message group chat mahasiswa gagal dikirim.', [
                'message_id' => $message->id,
                'room_id' => $message->room_id,
                'event' => $event,
                'error' => $exception->getMessage(),
            ]);
        }

        return $message;
    }

    private function scopeVisibleMembersQuery($query)
    {
        if (! GroupChatSupport::supportsMembershipStatus()) {
            return $query;
        }

        return $query->where('membership_status', GroupChatMember::STATUS_ACTIVE);
    }

    private function filterVisibleMembers($members)
    {
        if (! GroupChatSupport::supportsMembershipStatus()) {
            return $members;
        }

        return $members->filter(fn(GroupChatMember $member) => $member->membership_status === GroupChatMember::STATUS_ACTIVE)->values();
    }

    private function resolveInvitationAccess(User $user, string $token): array|RedirectResponse
    {
        if (! GroupChatSupport::supportsPrivateGroups()) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Fitur undangan grup privat belum aktif karena migration group chat terbaru belum dijalankan.');
        }

        $eligibility = GroupChatSupport::syncMemberEligibilityStatus($user);

        if (! ($eligibility['eligible'] ?? true)) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', $eligibility['reason'] ?? 'Anda tidak dapat bergabung ke grup privat saat ini.');
        }

        $room = GroupChatRoom::query()
            ->where('invite_token', $token)
            ->where('is_active', true)
            ->first();

        if (! $room || ! $room->isPrivate()) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Undangan grup privat tidak valid atau sudah tidak tersedia.');
        }

        $member = GroupChatMember::query()
            ->where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $member) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Undangan ini tidak terdaftar untuk akun Anda.');
        }

        if (GroupChatSupport::supportsMembershipStatus() && in_array($member->membership_status, [
            GroupChatMember::STATUS_BLOCKED,
            GroupChatMember::STATUS_REMOVED,
        ], true)) {
            return redirect()
                ->route('mahasiswa.group-chat')
                ->with('error', 'Akses ke grup privat ini tidak tersedia untuk akun Anda.');
        }

        return [
            'room' => $room,
            'member' => $member,
        ];
    }

    private function memberHasCurrentConsent(?GroupChatMember $member): bool
    {
        if (! $member) {
            return false;
        }

        if (! GroupChatSupport::supportsConsentTracking()) {
            return true;
        }

        return filled($member->consented_at)
            && filled($member->consent_version)
            && $member->consent_version === GroupChatSupport::consentVersion();
    }

    private function buildConsentPayload(?GroupChatRoom $room, string $topic, ?string $token, string $cancelUrl, string $mode): array
    {
        $topicLabel = $room?->topicLabel() ?: (GroupChatRoom::topicOptions()[$topic] ?? ucfirst(str_replace('_', ' ', $topic)));
        $roomTitle = $room?->title ?: ('Grup Konseling ' . $topicLabel);
        $isPrivate = $mode === 'private' || ($room?->isPrivate() ?? false);

        return [
            'title' => $roomTitle,
            'topic_label' => $topicLabel,
            'topic' => $topic,
            'room_id' => $room?->id,
            'token' => $token,
            'mode' => $isPrivate ? 'private' : 'public',
            'visibility_label' => $isPrivate ? 'Privat' : 'Publik',
            'submit_url' => route('mahasiswa.group-chat.join'),
            'cancel_url' => $cancelUrl,
            'rules' => GroupChatSupport::rules(),
            'description' => $isPrivate
                ? 'Undangan ini hanya berlaku untuk mahasiswa yang terdaftar di grup privat ini. Baca aturan grup dan setujui terlebih dahulu sebelum bergabung.'
                : 'Baca aturan grup terlebih dahulu. Setelah menyetujui, Anda akan masuk ke grup konseling dengan topik yang dipilih.',
            'note' => $isPrivate
                ? 'Setelah menyetujui aturan, Anda akan langsung masuk ke grup privat ini.'
                : 'Jika grup dengan topik ini sudah ada, sistem akan membukanya. Jika belum ada, grup baru akan dibuat otomatis.',
        ];
    }
}

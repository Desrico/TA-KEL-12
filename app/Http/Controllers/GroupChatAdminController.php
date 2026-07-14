<?php

namespace App\Http\Controllers;

use App\Events\GroupChatMessageSent;
use App\Models\GroupChatMember;
use App\Models\GroupChatMessage;
use App\Models\GroupChatRoom;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\NotifikasiMahasiswa;
use App\Models\Student;
use App\Services\KampusApiService;
use App\Models\User;
use App\Support\GroupChatSupport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupChatAdminController extends Controller
{
    public function index(Request $request)
    {
        $groupList = $this->resolveAvailableRooms();
        $activeRoom = $this->resolveSelectedRoom($groupList, $request->integer('group'));

        return view('admin.group-chat', [
            'groupList' => $groupList,
            'activeRoom' => $activeRoom,
            'chatPayload' => $activeRoom ? $this->buildChatPayload($activeRoom) : null,
        ]);
    }

    public function searchStudents(Request $request, KampusApiService $kampusApi): JsonResponse
    {
        $nim = preg_replace('/\s+/', '', (string) $request->query('nim', ''));
        $nim = is_string($nim) ? trim($nim) : '';
        $limit = 12;
        $autocompleteTimeout = 6;

        if (mb_strlen($nim) < 3) {
            return response()->json([
                'success' => true,
                'items' => [],
                'source' => 'idle',
            ]);
        }

        $room = null;
        $roomId = (int) $request->integer('room_id');

        if ($roomId > 0) {
            $room = GroupChatRoom::query()->find($roomId);
        }

        $cacheKey = 'admin.group_chat.student_lookup.' . $nim . '.' . $limit . '.' . ($room?->id ?? 'new');
        $cachedPayload = $room ? null : Cache::get($cacheKey);

        if (is_array($cachedPayload)) {
            return response()->json([
                'success' => true,
                'items' => $cachedPayload['items'] ?? [],
                'source' => $cachedPayload['source'] ?? 'cache',
                'message' => $cachedPayload['message'] ?? null,
            ]);
        }

        [$items, $source, $message] = $this->resolveStudentLookup(
            $nim,
            $kampusApi,
            (string) $request->session()->get('cis.access_token', ''),
            $limit,
            $autocompleteTimeout,
            $room
        );

        if (! $room && $items !== [] && $message === null) {
            Cache::put($cacheKey, [
                'items' => $items,
                'source' => $source,
                'message' => null,
            ], now()->addMinutes(2));
        }

        return response()->json([
            'success' => true,
            'items' => $items,
            'source' => $source,
            'message' => $message,
        ]);
    }

    public function createRoom(Request $request): RedirectResponse
    {
        $visibility = $request->input('visibility') === GroupChatRoom::VISIBILITY_PUBLIC
            ? GroupChatRoom::VISIBILITY_PUBLIC
            : GroupChatRoom::VISIBILITY_PRIVATE;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'invite_nims' => ['nullable', 'string'],
        ]);

        if ($visibility === GroupChatRoom::VISIBILITY_PRIVATE && ! GroupChatSupport::supportsPrivateGroups()) {
            return back()->with('error', 'Grup privat membutuhkan migration database group chat terbaru sebelum dapat digunakan.');
        }

        if ($visibility === GroupChatRoom::VISIBILITY_PUBLIC) {
            $room = DB::transaction(function () use ($request, $validated) {
                $title = trim($validated['title']);
                $topic = $this->makeUniquePublicTopicKey($title);

                return GroupChatRoom::query()->create([
                    'topic' => $topic,
                    'title' => $title,
                    'description' => 'Grup publik yang dibuat oleh konselor.',
                    'visibility' => GroupChatRoom::VISIBILITY_PUBLIC,
                    'created_by' => $request->user()->id,
                    'is_active' => true,
                ]);
            });

            return redirect()
                ->route('admin.group-chat', ['group' => $room->id])
                ->with('success', 'Grup publik "' . $room->title . '" berhasil dibuat dan akan muncul sebagai topik konseling mahasiswa.');
        }

        [$room, $inviteSummary] = DB::transaction(function () use ($request, $validated) {
            $room = GroupChatRoom::query()->create([
                'topic' => GroupChatSupport::makePrivateTopicKey(),
                'title' => trim($validated['title']),
                'description' => 'Grup privat yang dibuat oleh konselor.',
                'visibility' => GroupChatRoom::VISIBILITY_PRIVATE,
                'invite_token' => Str::random(48),
                'created_by' => $request->user()->id,
                'is_active' => true,
            ]);

            $inviteSummary = $this->inviteStudentsToRoom(
                $room,
                $request->user(),
                (string) ($validated['invite_nims'] ?? '')
            );

            return [$room, $inviteSummary];
        });

        return redirect()
            ->route('admin.group-chat', ['group' => $room->id])
            ->with('success', $this->buildInviteFeedbackMessage(
                'Grup privat berhasil dibuat.',
                $inviteSummary
            ))
            ->with('admin_success_modal', $this->buildPrivateGroupSuccessModalPayload(
                $room,
                'Grup privat "' . $room->title . '" telah berhasil dibuat.',
                $inviteSummary
            ));
    }

    private function makeUniquePublicTopicKey(string $title): string
    {
        $base = Str::slug($title, '_') ?: 'grup_publik';
        $topic = $base;
        $suffix = 2;

        while (GroupChatRoom::query()->where('topic', $topic)->exists()) {
            $topic = $base . '_' . $suffix;
            $suffix++;
        }

        return $topic;
    }

    public function inviteMembers(Request $request, GroupChatRoom $group): RedirectResponse
    {
        if (! GroupChatSupport::supportsPrivateGroups()) {
            return back()->with('error', 'Fitur undangan grup privat belum aktif karena migration database group chat terbaru belum dijalankan.');
        }

        $validated = $request->validate([
            'invite_nims' => ['required', 'string'],
        ]);

        if (! $group->isPrivate()) {
            return back()->with('error', 'Undangan tambahan hanya tersedia untuk grup privat.');
        }

        $inviteSummary = DB::transaction(function () use ($group, $request, $validated) {
            if (! filled($group->invite_token)) {
                $group->update(['invite_token' => Str::random(48)]);
            }

            return $this->inviteStudentsToRoom($group, $request->user(), $validated['invite_nims']);
        });

        $redirect = redirect()->route('admin.group-chat', ['group' => $group->id]);

        if (! $this->hasSuccessfulInviteOutcome($inviteSummary)) {
            return $redirect->with('error', $this->buildInviteFeedbackMessage(
                'Belum ada undangan yang berhasil diproses.',
                $inviteSummary
            ));
        }

        return $redirect
            ->with('success', $this->buildInviteFeedbackMessage(
                'Undangan grup privat berhasil diproses.',
                $inviteSummary
            ))
            // modal bahwa berhasil mengundang mahasiswa aktif sekian anggota.
            ->with('admin_invite_success_modal', $this->buildPrivateGroupInviteSuccessModalPayload($group, $inviteSummary));
    }

    public function renameRoom(Request $request, GroupChatRoom $group): RedirectResponse
    {
        if (! GroupChatSupport::supportsPrivateGroups()) {
            return back()->with('error', 'Rename grup privat membutuhkan migration database group chat terbaru sebelum dapat digunakan.');
        }

        if (! $group->isPrivate() || ! $group->is_active) {
            return back()->with('error', 'Hanya grup privat aktif yang dapat diubah namanya.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
        ]);

        // Rename dibatasi ke judul agar topik privat dan struktur undangan yang sudah ada tidak ikut berubah.
        $group->update([
            'title' => trim($validated['title']),
        ]);

        return redirect()
            ->route('admin.group-chat', ['group' => $group->id])
            ->with('success', 'Nama grup privat berhasil diperbarui.');
    }

    public function updateRoomAvatar(Request $request, GroupChatRoom $group): RedirectResponse
    {
        $room = $this->resolveRoom($group->id);

        if (! $room) {
            return back()->with('error', 'Grup chat tidak ditemukan.');
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
            ->route('admin.group-chat', ['group' => $room->id])
            ->with('success', 'Foto grup berhasil diperbarui.');
    }

    public function deleteRoom(GroupChatRoom $group): RedirectResponse
    {
        $room = $this->resolveRoom($group->id);

        if (! $room) {
            return redirect()
                ->route('admin.group-chat')
                ->with('error', 'Grup chat tidak ditemukan.');
        }

        if (! $room->isPrivate()) {
            return redirect()
                ->route('admin.group-chat', ['group' => $room->id])
                ->with('error', 'Grup publik tidak dapat dihapus.');
        }

        $roomTitle = $room->title;
        $avatarPath = $room->avatar_path;
        $inviteUrl = GroupChatSupport::supportsInviteToken() && filled($room->invite_token)
            ? route('mahasiswa.group-chat.invite', ['token' => $room->invite_token])
            : null;

        DB::transaction(function () use ($room, $inviteUrl) {
            if ($inviteUrl) {
                Notifikasi::query()
                    ->where('cta_target', $inviteUrl)
                    ->delete();

                try {
                    NotifikasiMahasiswa::query()
                        ->where('cta_target', $inviteUrl)
                        ->delete();
                } catch (\Throwable $exception) {
                    Log::warning('Penghapusan notifikasi Mongo untuk grup privat gagal.', [
                        'room_id' => $room->id,
                        'invite_url' => $inviteUrl,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            $room->delete();
        });

        if (filled($avatarPath)) {
            Storage::disk('public')->delete($avatarPath);
        }

        return redirect()
            ->route('admin.group-chat')
            ->with('success', 'Grup privat "' . $roomTitle . '" berhasil dihapus.')
            ->with('admin_private_group_deleted_modal', [
                'title' => 'Grup Privat Berhasil Dihapus',
                'message' => 'Grup privat "' . $roomTitle . '" telah berhasil dihapus dari sistem.',
            ]);
    }

    public function removeMember(Request $request, GroupChatRoom $group, GroupChatMember $member): RedirectResponse
    {
        $room = $this->resolveRoom($group->id);

        if (! $room || (int) $member->room_id !== (int) $group->id) {
            return back()->with('error', 'Anggota grup tidak ditemukan.');
        }

        $member->loadMissing('user.mahasiswa');

        if (GroupChatSupport::supportsMembershipStatus() && $member->membership_status !== GroupChatMember::STATUS_ACTIVE) {
            return redirect()
                ->route('admin.group-chat', ['group' => $group->id])
                ->with('error', 'Anggota tersebut sudah tidak aktif di grup.');
        }

        $memberName = GroupChatSupport::resolveDisplayName($member->user, $room, $member);

        DB::transaction(function () use ($member) {
            if (GroupChatSupport::supportsMembershipStatus()) {
                $payload = [
                    'membership_status' => GroupChatMember::STATUS_REMOVED,
                ];

                if (GroupChatSupport::supportsMembershipLifecycleFields()) {
                    $payload['removed_at'] = now();
                    $payload['removed_reason'] = 'removed_by_counselor';
                }

                $member->update($payload);

                return;
            }

            $member->delete();
        });

        $this->createMembershipSystemMessage($room, $member->user, 'removed');

        return redirect()
            ->route('admin.group-chat', ['group' => $group->id])
            ->with('success', 'Anggota grup berhasil dikeluarkan.')
            ->with('admin_member_action_modal', [
                'title' => 'Anggota Berhasil Dikeluarkan',
                'message' => $memberName . ' berhasil dikeluarkan dari grup privat.',
            ]);
    }

    public function messages(Request $request): JsonResponse
    {
        $room = $this->resolveRoom((int) $request->integer('group_id'));

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

    public function members(Request $request): JsonResponse
    {
        $room = $this->resolveRoom((int) $request->integer('group_id'));

        if (! $room) {
            return response()->json([
                'success' => false,
                'message' => 'Grup chat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'members' => $this->resolveMemberProfiles($room),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'pesan' => 'required|string|max:2000',
            'reply_to_id' => 'nullable|integer',
        ]);

        $room = $this->resolveRoom((int) $validated['group_id']);

        if (! $room) {
            return response()->json([
                'success' => false,
                'message' => 'Grup chat tidak ditemukan.',
            ], 404);
        }

        $replyToMessage = $this->resolveReplyMessageForRoom($room, $validated['reply_to_id'] ?? null);

        $message = DB::transaction(function () use ($room, $request, $validated, $replyToMessage) {
            $payload = [
                'room_id' => $room->id,
                'user_id' => $request->user()->id,
                'pesan' => trim($validated['pesan']),
                'reply_to_message_id' => $replyToMessage?->id,
            ];

            if (GroupChatSupport::supportsSystemMessages()) {
                $payload['is_system'] = false;
                $payload['system_event'] = null;
            }

            return GroupChatMessage::create($payload);
        });

        $message->loadMissing([
            'sender.mahasiswa',
            'replyTo.sender',
            'room',
        ]);

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($message, $request->user(), $room),
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

        // Konselor hanya dapat mengubah pesan grup yang dia kirim sendiri.
        $message->update([
            'pesan' => trim($validated['pesan']),
        ]);

        $message->refresh()->loadMissing([
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

    private function resolveAvailableRooms()
    {
        // Sidebar tetap SSR, tetapi hanya membawa ringkasan room agar render awal tetap ringan.
        $roomsQuery = GroupChatRoom::query()
            ->with([
                'latestMessage.sender.mahasiswa',
            ])
            ->where('is_active', true);

        if (GroupChatSupport::supportsMembershipStatus()) {
            $roomsQuery
                ->withCount([
                    'members as active_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
                    'members as invited_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_INVITED),
                ]);
        } else {
            $roomsQuery->withCount('members');
        }

        $rooms = $roomsQuery->get()
            ->sortByDesc(function (GroupChatRoom $room) {
                return optional($room->latestMessage)->created_at?->getTimestamp()
                    ?? optional($room->updated_at)?->getTimestamp()
                    ?? 0;
            })
            ->values();

        return $rooms;
    }

    private function resolveSelectedRoom($groupList, ?int $roomId): ?GroupChatRoom
    {
        if ($roomId) {
            $room = $groupList->firstWhere('id', $roomId);

            if ($room) {
                if (GroupChatSupport::supportsMembershipStatus()) {
                    $room->loadMissing([
                        'members' => fn($query) => $query
                            ->where('membership_status', GroupChatMember::STATUS_ACTIVE)
                            ->with('user.mahasiswa'),
                    ]);
                } else {
                    $room->loadMissing([
                        'members.user.mahasiswa',
                    ]);
                }

                return $room;
            }
        }

        $room = $groupList->first();

        if ($room) {
            if (GroupChatSupport::supportsMembershipStatus()) {
                $room->loadMissing([
                    'members' => fn($query) => $query
                        ->where('membership_status', GroupChatMember::STATUS_ACTIVE)
                        ->with('user.mahasiswa'),
                ]);
            } else {
                $room->loadMissing([
                    'members.user.mahasiswa',
                ]);
            }
        }

        return $room;
    }

    private function resolveRoom(?int $roomId): ?GroupChatRoom
    {
        if (! $roomId) {
            return null;
        }

        $roomQuery = GroupChatRoom::query()
            ->whereKey($roomId)
            ->where('is_active', true);

        if (GroupChatSupport::supportsMembershipStatus()) {
            $roomQuery
                ->withCount([
                    'members as active_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_ACTIVE),
                    'members as invited_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_INVITED),
                ])
                ->with([
                    'members' => fn($query) => $query
                        ->where('membership_status', GroupChatMember::STATUS_ACTIVE)
                        ->with('user.mahasiswa'),
                ]);
        } else {
            $roomQuery
                ->withCount('members')
                ->with([
                    'members.user.mahasiswa',
                ]);
        }

        $room = $roomQuery->first();

        if ($room) {
            foreach ($room->members as $member) {
                GroupChatSupport::ensureMemberAlias($room, $member);
            }
        }

        return $room;
    }

    private function buildChatPayload(GroupChatRoom $room): array
    {
        return [
            'roomId' => $room->id,
            'channel' => 'chat.group.' . $room->id,
            'sendUrl' => route('admin.group-chat.store'),
            'messagesUrl' => route('admin.group-chat.messages'),
            'membersUrl' => route('admin.group-chat.members'),
            'updateUrlTemplate' => route('admin.group-chat.update', ['message' => '__MESSAGE_ID__']),
            'deleteUrlTemplate' => route('admin.group-chat.destroy', ['message' => '__MESSAGE_ID__']),
            'roomTitle' => $room->title,
            'roomDescription' => $room->description,
            'topicLabel' => $room->topicLabel(),
            'visibilityLabel' => $room->visibilityLabel(),
            'canRenameRoom' => GroupChatSupport::supportsPrivateGroups() && $room->isPrivate(),
            'memberCount' => (int) ($room->active_members_count ?? $room->members_count ?? $room->members->count()),
            'pendingInviteCount' => (int) ($room->invited_members_count ?? 0),
            'memberNames' => $this->resolveMemberNames($room),
            'roomAvatarUrl' => GroupChatSupport::resolveRoomAvatarUrl($room),
            'roomAvatarInitial' => GroupChatSupport::resolveRoomAvatarInitial($room),
            'canUpdateAvatar' => GroupChatSupport::supportsRoomAvatar(),
            'inviteUrl' => GroupChatSupport::supportsPrivateGroups() && $room->isPrivate() && filled($room->invite_token)
                ? route('mahasiswa.group-chat.invite', ['token' => $room->invite_token])
                : null,
            'canInviteMembers' => GroupChatSupport::supportsPrivateGroups() && $room->isPrivate(),
            'updateAvatarUrl' => route('admin.group-chat.rooms.avatar', ['group' => $room->id]),
            'privateMemberLimit' => GroupChatSupport::privateGroupMemberLimit(),
        ];
    }

    private function resolveMemberNames(GroupChatRoom $room): array
    {
        // Nama anggota harus mengikuti mode identitas room: publik anonim, privat nama asli.
        return $room->members
            ->sortBy(fn(GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(function (GroupChatMember $member) use ($room) {
                return GroupChatSupport::resolveDisplayName($member->user, $room, $member);
            })
            ->filter()
            ->values()
            ->all();
    }

    private function resolveMemberProfiles(GroupChatRoom $room): array
    {
        // Payload anggota dipakai ulang di header, dropdown anggota, dan pencarian sisi admin.
        return $room->members
            ->sortBy(fn(GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(function (GroupChatMember $member) use ($room) {
                return [
                    'member_id' => $member->id,
                    'user_id' => $member->user_id,
                    'name' => GroupChatSupport::resolveDisplayName($member->user, $room, $member),
                    'avatar_url' => GroupChatSupport::resolveAvatarUrl(),
                    'can_remove' => true,
                    'remove_url' => route('admin.group-chat.rooms.members.remove', ['group' => $room->id, 'member' => $member->id]),
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

        // Nama pengirim pesan admin juga harus tunduk pada aturan identitas room yang sama.
        return [
            'id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->user_id,
            'sender_name' => (bool) $message->is_system
                ? 'Sistem'
                : ($message->user_id === $viewer->id
                    ? 'Anda'
                    : GroupChatSupport::resolveDisplayName($sender, $room, $membership)),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => GroupChatSupport::resolveAvatarUrl(),
            'text' => $message->pesan,
            'reply_to' => $replyTo ? [
                'id' => $replyTo->id,
                'sender_id' => $replyTo->user_id,
                'sender_name' => (int) $replyTo->user_id === (int) $viewer->id
                    ? 'Anda'
                    : GroupChatSupport::resolveDisplayName($replySender, $room, $replyMembership),
                'text' => $replyTo->pesan,
            ] : null,
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
        $message->loadMissing('room');

        $room = $message->room;

        if (! $room || ! $room->is_active) {
            return null;
        }

        return $user->role === 'konselor' ? $room : null;
    }

    // Lookup mahasiswa untuk direct add dibuat hybrid: coba CIS lebih dulu jika service account tersedia,
    // lalu hasilnya tetap harus dipetakan ke akun lokal aplikasi agar undangan bisa benar-benar dikirim.
    private function resolveStudentLookup(
        string $keyword,
        KampusApiService $kampusApi,
        string $sessionToken = '',
        int $limit = 12,
        ?int $timeout = null,
        ?GroupChatRoom $room = null
    ): array {
        $cisRows = [];
        $source = 'local';
        $message = null;
        $activeToken = trim($sessionToken);

        if ($activeToken !== '') {
            try {
                $cisRows = $kampusApi->searchActiveMahasiswaByNim($keyword, $limit, $activeToken, $timeout);
                if ($cisRows !== []) {
                    $source = 'cis';
                }
            } catch (\Throwable $exception) {
                Log::warning('Autocomplete mahasiswa group chat gagal mengambil data dari CIS, fallback ke data lokal.', [
                    'keyword' => $keyword,
                    'auth_mode' => 'session_token',
                    'error' => $exception->getMessage(),
                ]);

                $message = 'Sesi CIS untuk akun ini sudah tidak valid, sistem mencoba fallback pencarian lainnya.';
            }
        }

        if ($cisRows === [] && $kampusApi->hasStaticToken()) {
            try {
                $cisRows = $kampusApi->searchActiveMahasiswaByNim($keyword, $limit, null, $timeout);
                if ($cisRows !== []) {
                    $source = 'cis';
                    $message = null;
                }
            } catch (\Throwable $exception) {
                Log::warning('Autocomplete mahasiswa group chat gagal mengambil data dari CIS, fallback ke data lokal.', [
                    'keyword' => $keyword,
                    'auth_mode' => 'static_token',
                    'error' => $exception->getMessage(),
                ]);

                $message = 'Mahasiswa dengan NIM tersebut tidak ditemukan. Pastikan NIM yang dimasukkan sudah benar.';
            }
        }

        if ($cisRows === [] && $kampusApi->isConfigured()) {
            try {
                $cisRows = $kampusApi->searchActiveMahasiswaByNim($keyword, $limit, null, $timeout);
                if ($cisRows !== []) {
                    $source = 'cis';
                    $message = null;
                }
            } catch (\Throwable $exception) {
                Log::warning('Autocomplete mahasiswa group chat gagal mengambil data dari CIS, fallback ke data lokal.', [
                    'keyword' => $keyword,
                    'auth_mode' => 'service_account',
                    'error' => $exception->getMessage(),
                ]);

                $message = str_contains(Str::lower($exception->getMessage()), 'akun user tidak valid')
                    ? 'Kredensial service account CIS belum valid, jadi pencarian masih memakai data lokal aplikasi.'
                    : 'Pencarian CIS sedang tidak tersedia, sistem memakai data lokal yang sudah tersinkron.';
            }
        } elseif ($cisRows === [] && $message === null) {
            $message = 'Pencarian masih memakai data lokal karena token CIS atau service account CIS belum tersedia.';
        }

        // Hasil CIS yang sudah dipastikan aktif langsung disinkronkan ke akun lokal
        // agar autocomplete tidak gagal hanya karena mapping lokal tertinggal.
        $this->syncMahasiswaFromCisRows($cisRows);

        $directory = $this->buildStudentDirectory($keyword, $cisRows, $limit);
        $nimOrder = array_keys($directory);

        if ($nimOrder === []) {
            return [[], $source, $message];
        }

        $cisActiveNims = collect($cisRows)
            ->map(fn(array $row) => trim((string) ($row['nim'] ?? '')))
            ->filter()
            ->values()
            ->all();

        $invitableStudents = $this->resolveInvitableMahasiswaByNims($nimOrder, $cisActiveNims);
        $roomInviteStatuses = $room ? $this->resolveInviteStatusesForRoom($room) : [];

        $items = collect($nimOrder)
            ->map(function (string $nim) use ($directory, $invitableStudents, $roomInviteStatuses) {
                $directoryItem = $directory[$nim] ?? [];
                $displayName = trim((string) (
                    $directoryItem['name']
                    ?? 'Mahasiswa'
                ));

                $status = $roomInviteStatuses[$nim] ?? null;

                if (! $invitableStudents->has($nim) && $status === null) {
                    return null;
                }

                if ($status !== null) {
                    return [
                        'nim' => $nim,
                        'name' => $displayName !== '' ? $displayName : 'Mahasiswa',
                        'label' => $nim . ' - ' . ($displayName !== '' ? $displayName : 'Mahasiswa'),
                        'source' => $directoryItem['source'] ?? 'cis',
                        'selectable' => false,
                        'status' => $status,
                        'note' => $status === 'already_invited'
                            ? 'Mahasiswa ini sudah diundang.'
                            : 'Mahasiswa ini telah bergabung.',
                    ];
                }

                return [
                    'nim' => $nim,
                    'name' => $displayName !== '' ? $displayName : 'Mahasiswa',
                    'label' => $nim . ' - ' . ($displayName !== '' ? $displayName : 'Mahasiswa'),
                    'source' => $directoryItem['source'] ?? 'cis',
                    'selectable' => true,
                ];
            })
            ->filter()
            ->take($limit)
            ->values()
            ->all();

        if ($items === [] && $message === null) {
            $message = 'NIM mahasiswa ditemukan, tetapi akun mahasiswa di aplikasi yang masih aktif dan bisa menerima undangan grup privat belum tersedia.';
        }

        return [$items, $source, $message];
    }

    private function buildStudentDirectory(string $keyword, array $cisRows = [], int $limit = 12): array
    {
        $directory = [];

        foreach ($cisRows as $row) {
            $nim = trim((string) ($row['nim'] ?? ''));
            if ($nim === '') {
                continue;
            }

            $directory[$nim] = [
                'nim' => $nim,
                'name' => trim((string) ($row['name'] ?? 'Mahasiswa')),
                'source' => 'cis',
            ];
        }

        // Fallback lokal tetap dipertahankan agar fitur admin masih berguna saat CIS belum siap atau sedang timeout.
        Student::query()
            ->where('nim', 'like', $keyword . '%')
            ->limit($limit)
            ->get(['nim', 'name'])
            ->each(function (Student $student) use (&$directory) {
                $nim = trim((string) $student->nim);
                if ($nim === '') {
                    return;
                }

                $directory[$nim] ??= [
                    'nim' => $nim,
                    'name' => trim((string) ($student->name ?? 'Mahasiswa')),
                    'source' => 'student_local',
                ];
            });

        Mahasiswa::query()
            ->with('user:id,nama,role')
            ->where('nim', 'like', $keyword . '%')
            ->limit($limit)
            ->get(['id', 'user_id', 'nim'])
            ->each(function (Mahasiswa $mahasiswa) use (&$directory) {
                $nim = trim((string) $mahasiswa->nim);
                $user = $mahasiswa->user;

                if ($nim === '' || ! $user || $user->role !== 'mahasiswa') {
                    return;
                }

                if (($directory[$nim]['source'] ?? null) === 'cis') {
                    return;
                }

                $directory[$nim] = [
                    'nim' => $nim,
                    'name' => trim((string) ($user->nama ?? 'Mahasiswa')),
                    'source' => 'app_local',
                ];
            });

        return $directory;
    }

    private function resolveInvitableMahasiswaByNims(array $nims, array $cisActiveNims = [])
    {
        return Mahasiswa::query()
            ->with('user:id,nama,role')
            ->whereIn('nim', $nims)
            ->get(['id', 'user_id', 'nim'])
            ->filter(fn(Mahasiswa $mahasiswa) => $mahasiswa->user
                && $mahasiswa->user->role === 'mahasiswa'
                && $this->isUserEligibleForInvite($mahasiswa->user, (string) $mahasiswa->nim, $cisActiveNims))
            ->keyBy(fn(Mahasiswa $mahasiswa) => (string) $mahasiswa->nim);
    }

    private function resolveInviteStatusesForRoom(GroupChatRoom $room): array
    {
        return $room->members()
            ->with('user.mahasiswa:id,user_id,nim')
            ->whereIn('membership_status', [
                GroupChatMember::STATUS_ACTIVE,
                GroupChatMember::STATUS_INVITED,
            ])
            ->get()
            ->mapWithKeys(function (GroupChatMember $member) {
                $nim = trim((string) optional(optional($member->user)->mahasiswa)->nim);

                if ($nim === '') {
                    return [];
                }

                return [
                    $nim => $member->membership_status === GroupChatMember::STATUS_INVITED
                        ? 'already_invited'
                        : 'already_joined',
                ];
            })
            ->filter()
            ->all();
    }

    private function parseNimList(string $rawValue): array
    {
        return collect(preg_split('/[\s,;]+/', $rawValue) ?: [])
            ->map(fn($nim) => trim((string) $nim))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function inviteStudentsToRoom(GroupChatRoom $room, User $inviter, string $rawNimList): array
    {
        $nims = $this->parseNimList($rawNimList);

        if ($nims === []) {
            return [
                'invited_count' => 0,
                'already_member_count' => 0,
                'already_invited_count' => 0,
                'missing_nims' => [],
                'unavailable_nims' => [],
                'blocked_nims' => [],
                'capacity_limited_nims' => [],
            ];
        }

        $cisActiveNims = $this->syncMahasiswaFromCisByNims($nims);

        $students = Mahasiswa::query()
            ->with('user')
            ->whereIn('nim', $nims)
            ->get()
            ->keyBy(fn(Mahasiswa $mahasiswa) => (string) $mahasiswa->nim);

        $invitedCount = 0;
        $alreadyMemberCount = 0;
        $alreadyInvitedCount = 0;
        $blockedNims = [];
        $unavailableNims = [];
        $capacityLimitedNims = [];
        $remainingSlots = $room->isPrivate() ? $this->resolveRemainingPrivateSlots($room) : null;

        foreach ($nims as $nim) {
            if ($room->isPrivate() && $remainingSlots !== null && $remainingSlots <= 0) {
                $capacityLimitedNims[] = $nim;
                continue;
            }

            /** @var Mahasiswa|null $mahasiswa */
            $mahasiswa = $students->get($nim);
            $user = $mahasiswa?->user;

            if (! $user || $user->role !== 'mahasiswa') {
                $unavailableNims[] = $nim;
                continue;
            }

            if (! $this->isUserEligibleForInvite($user, $nim, $cisActiveNims)) {
                $blockedNims[] = $nim;
                continue;
            }

            $member = GroupChatMember::query()->firstOrNew([
                'room_id' => $room->id,
                'user_id' => $user->id,
            ]);

            if ($member->exists && (! GroupChatSupport::supportsMembershipStatus() || $member->membership_status === GroupChatMember::STATUS_ACTIVE)) {
                $alreadyMemberCount++;
                continue;
            }

            if ($member->exists && GroupChatSupport::supportsMembershipStatus() && $member->membership_status === GroupChatMember::STATUS_INVITED) {
                $alreadyInvitedCount++;
                continue;
            }

            if (
                $member->exists
                && GroupChatSupport::supportsMembershipStatus()
                && $member->membership_status === GroupChatMember::STATUS_BLOCKED
            ) {
                $blockedNims[] = $nim;
                continue;
            }

            $memberFill = [];

            if (GroupChatSupport::supportsMembershipStatus()) {
                $memberFill['membership_status'] = GroupChatMember::STATUS_INVITED;
            }

            if (GroupChatSupport::supportsMembershipLifecycleFields()) {
                $memberFill['invited_by'] = $inviter->id;
                $memberFill['joined_via'] = 'direct_add';
                $memberFill['removed_at'] = null;
                $memberFill['removed_reason'] = null;
            }

            $member->fill($memberFill);
            $member->save();

            GroupChatSupport::ensureMemberAlias($room, $member);

            $notificationPayload = [
                'user_id' => $user->id,
                // Notifikasi undangan privat harus mengarahkan mahasiswa ke consent sebelum grup benar-benar aktif untuknya.
                'pesan' => 'Anda telah diundang ke grup "' . $room->title . '". Silakan buka consent grup sebelum bergabung.',
                'status' => 'belum',
            ];

            if (GroupChatSupport::supportsNotificationCtas()) {
                $notificationPayload['cta_target'] = route('mahasiswa.group-chat.invite', ['token' => $room->invite_token]);
                $notificationPayload['cta_label'] = 'Buka Undangan Grup';
            }

            Notifikasi::create($notificationPayload);

            $invitedCount++;

            if ($room->isPrivate() && $remainingSlots !== null) {
                $remainingSlots--;
            }
        }

        $missingNims = array_values(array_diff($nims, $students->keys()->all()));
        $studentOnlyNims = Student::query()
            ->whereIn('nim', $missingNims)
            ->pluck('nim')
            ->map(fn($nim) => trim((string) $nim))
            ->filter()
            ->values()
            ->all();

        if ($studentOnlyNims !== []) {
            $unavailableNims = array_values(array_unique(array_merge(
                $unavailableNims,
                array_values(array_intersect($missingNims, $studentOnlyNims))
            )));
            $missingNims = array_values(array_diff($missingNims, $studentOnlyNims));
        }

        return [
            'invited_count' => $invitedCount,
            'already_member_count' => $alreadyMemberCount,
            'already_invited_count' => $alreadyInvitedCount,
            'missing_nims' => $missingNims,
            'unavailable_nims' => $unavailableNims,
            'blocked_nims' => $blockedNims,
            'capacity_limited_nims' => $capacityLimitedNims,
        ];
    }

    // Grup privat dibatasi agar dinamika konseling tetap terkelola dan tidak terlalu padat.
    private function resolveRemainingPrivateSlots(GroupChatRoom $room): int
    {
        if (! $room->isPrivate()) {
            return PHP_INT_MAX;
        }

        $currentCount = GroupChatMember::query()
            ->where('room_id', $room->id)
            ->when(
                GroupChatSupport::supportsMembershipStatus(),
                fn($query) => $query->whereIn('membership_status', [
                    GroupChatMember::STATUS_ACTIVE,
                    GroupChatMember::STATUS_INVITED,
                ])
            )
            ->count();

        return max(0, GroupChatSupport::privateGroupMemberLimit() - $currentCount);
    }

    private function buildVisibleMessages(GroupChatRoom $room, User $viewer): array
    {
        return $room->messages()
            ->with([
                'sender.mahasiswa',
                'replyTo.sender',
            ])
            ->orderBy('created_at')
            ->get()
            ->map(fn(GroupChatMessage $message) => $this->transformMessage($message, $viewer, $room))
            ->all();
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

        return $message;
    }

    private function syncMahasiswaFromCisByNims(array $nims): array
    {
        /** @var KampusApiService $kampusApi */
        $kampusApi = app(KampusApiService::class);
        $activeNims = [];

        foreach (array_values(array_unique($nims)) as $nim) {
            try {
                $cisRow = $kampusApi->findActiveMahasiswaByExactNim($nim);

                if (! $cisRow || trim((string) ($cisRow['nim'] ?? '')) === '') {
                    continue;
                }

                $activeNims[] = trim((string) ($cisRow['nim'] ?? ''));
                $this->syncMahasiswaFromCisRow($cisRow);
            } catch (\Throwable $exception) {
                Log::warning('Sinkronisasi mahasiswa CIS untuk undangan group chat gagal.', [
                    'nim' => $nim,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return array_values(array_unique(array_filter($activeNims)));
    }

    private function syncMahasiswaFromCisRows(array $cisRows): void
    {
        foreach ($cisRows as $cisRow) {
            if (! is_array($cisRow) || trim((string) ($cisRow['nim'] ?? '')) === '') {
                continue;
            }

            try {
                $this->syncMahasiswaFromCisRow($cisRow);
            } catch (\Throwable $exception) {
                Log::warning('Sinkronisasi hasil pencarian CIS untuk group chat gagal.', [
                    'nim' => trim((string) ($cisRow['nim'] ?? '')),
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function syncMahasiswaFromCisRow(array $cisRow): ?Mahasiswa
    {
        $nim = trim((string) ($cisRow['nim'] ?? ''));

        if ($nim === '') {
            return null;
        }

        $existingMahasiswa = Mahasiswa::query()->with('user')->where('nim', $nim)->first();

        if ($existingMahasiswa?->user && $existingMahasiswa->user->role === 'mahasiswa') {
            return $existingMahasiswa;
        }

        $usernameCis = trim((string) ($cisRow['username'] ?? $nim));
        $email = trim((string) ($cisRow['email'] ?? ''));
        $fallbackEmail = ($usernameCis !== '' ? $usernameCis : $nim) . '@cis.local';

        $user = User::query()
            ->where(function ($query) use ($usernameCis, $email) {
                if ($usernameCis !== '') {
                    $query->where('username_cis', $usernameCis);
                }

                if ($email !== '') {
                    if ($usernameCis !== '') {
                        $query->orWhere('email', $email);
                    } else {
                        $query->where('email', $email);
                    }
                }
            })
            ->first();

        if ($user && $user->role !== 'mahasiswa' && ! $user->mahasiswa) {
            return null;
        }

        $user ??= new User();
        $user->nama = trim((string) ($cisRow['name'] ?? 'Mahasiswa')) ?: 'Mahasiswa';
        $user->email = $email !== '' ? $email : ($user->email ?: $fallbackEmail);
        $user->username_cis = $usernameCis !== '' ? $usernameCis : ($user->username_cis ?: $nim);
        $user->role = 'mahasiswa';

        if (! $user->exists || empty($user->password)) {
            $user->password = bcrypt(Str::random(16));
        }

        $user->save();

        Mahasiswa::updateOrCreate(
            ['nim' => $nim],
            [
                'user_id' => $user->id,
                'jurusan' => trim((string) ($cisRow['prodi'] ?? '')) ?: '-',
                'angkatan' => trim((string) ($cisRow['angkatan'] ?? '')) ?: $this->extractAngkatanFromNim($nim),
            ]
        );

        try {
            // Snapshot Student lokal hanya pelengkap untuk cache/status.
            // Kegagalan sinkronisasi Mongo tidak boleh membatalkan mapping user-mahasiswa SQL.
            $this->syncStudentSnapshotFromCisRow($cisRow);
        } catch (\Throwable $exception) {
            Log::warning('Sinkronisasi snapshot Student dari hasil CIS gagal, tetapi mapping akun lokal tetap dilanjutkan.', [
                'nim' => $nim,
                'error' => $exception->getMessage(),
            ]);
        }

        return Mahasiswa::query()->with('user')->where('nim', $nim)->first();
    }

    private function isUserEligibleForInvite(User $user, string $nim = '', array $cisActiveNims = []): bool
    {
        if ($nim !== '' && in_array($nim, $cisActiveNims, true)) {
            return true;
        }

        return GroupChatSupport::resolveAcademicEligibility($user)['eligible'];
    }

    private function syncStudentSnapshotFromCisRow(array $cisRow): void
    {
        $nim = trim((string) ($cisRow['nim'] ?? ''));

        if ($nim === '') {
            return;
        }

        $student = Student::query()->where('nim', $nim)->first() ?? new Student();
        $student->nim = $nim;
        $student->name = trim((string) ($cisRow['name'] ?? '')) ?: ($student->name ?? 'Mahasiswa');

        $prodi = trim((string) ($cisRow['prodi'] ?? ''));
        if ($prodi !== '') {
            $student->prodi = $prodi;
        }

        $angkatan = trim((string) ($cisRow['angkatan'] ?? '')) ?: $this->extractAngkatanFromNim($nim);
        if ($angkatan) {
            $student->angkatan = $angkatan;
            $student->tingkatan = $student->tingkatan ?? $angkatan;
        }

        // Search endpoint CIS khusus meminta status Aktif, jadi snapshot lokal
        // disegarkan agar eligibility check tidak tertahan oleh data lama.
        $student->status = 'Aktif';
        $student->status_mahasiswa = 'Aktif';
        $student->status_akademik = 'Aktif';
        $student->academic_status = 'Aktif';
        $student->is_active = true;
        $student->is_active_student = true;
        $student->active = true;

        if (isset($student->graduated_at)) {
            $student->graduated_at = null;
        }

        if (isset($student->tanggal_lulus)) {
            $student->tanggal_lulus = null;
        }

        $student->save();
    }

    private function extractAngkatanFromNim(string $nim): ?string
    {
        $nim = trim($nim);

        if (strlen($nim) < 2) {
            return null;
        }

        return '20' . substr($nim, 0, 2);
    }

    private function buildInviteFeedbackMessage(string $prefix, array $summary): string
    {
        $parts = [$prefix];

        if ($summary['invited_count'] > 0) {
            $parts[] = $summary['invited_count'] . ' mahasiswa menerima undangan.';
        }

        if ($summary['already_member_count'] > 0) {
            $parts[] = $summary['already_member_count'] . ' mahasiswa sudah menjadi anggota aktif.';
        }

        if (($summary['already_invited_count'] ?? 0) > 0) {
            $parts[] = $summary['already_invited_count'] . ' mahasiswa sebelumnya sudah menerima undangan.';
        }

        if ($summary['missing_nims'] !== []) {
            $parts[] = 'NIM tidak ditemukan: ' . implode(', ', $summary['missing_nims']) . '.';
        }

        if (($summary['unavailable_nims'] ?? []) !== []) {
            $parts[] = 'Data ditemukan, tetapi akun mahasiswa aktif untuk menerima undangan belum tersedia: ' . implode(', ', $summary['unavailable_nims']) . '.';
        }

        if ($summary['blocked_nims'] !== []) {
            $parts[] = 'Beberapa undangan dilewati karena status anggota dibatasi: ' . implode(', ', $summary['blocked_nims']) . '.';
        }

        if (($summary['capacity_limited_nims'] ?? []) !== []) {
            $parts[] = 'Batas maksimal ' . GroupChatSupport::privateGroupMemberLimit() . ' anggota privat tercapai, sehingga undangan dilewati untuk: ' . implode(', ', $summary['capacity_limited_nims']) . '.';
        }

        return implode(' ', $parts);
    }

    private function buildPrivateGroupSuccessModalPayload(GroupChatRoom $room, string $message, array $summary): array
    {
        $detailLines = [];

        if (($summary['invited_count'] ?? 0) > 0) {
            $detailLines[] = $summary['invited_count'] . ' mahasiswa menerima notifikasi undangan.';
        }

        if (($summary['already_member_count'] ?? 0) > 0) {
            $detailLines[] = $summary['already_member_count'] . ' mahasiswa sudah aktif di grup ini.';
        }

        if (($summary['already_invited_count'] ?? 0) > 0) {
            $detailLines[] = $summary['already_invited_count'] . ' mahasiswa sebelumnya sudah tercatat sebagai penerima undangan.';
        }

        if (! empty($summary['missing_nims'])) {
            $detailLines[] = 'NIM tidak ditemukan: ' . implode(', ', $summary['missing_nims']) . '.';
        }

        if (! empty($summary['unavailable_nims'])) {
            $detailLines[] = 'Data ditemukan, tetapi akun mahasiswa aktif untuk menerima undangan belum tersedia: ' . implode(', ', $summary['unavailable_nims']) . '.';
        }

        if (! empty($summary['blocked_nims'])) {
            $detailLines[] = 'Undangan dilewati untuk status anggota terbatas: ' . implode(', ', $summary['blocked_nims']) . '.';
        }

        if (! empty($summary['capacity_limited_nims'])) {
            $detailLines[] = 'Kuota grup privat maksimal ' . GroupChatSupport::privateGroupMemberLimit() . ' anggota sudah penuh: ' . implode(', ', $summary['capacity_limited_nims']) . '.';
        }

        return [
            'title' => 'Grup Privat Berhasil',
            'message' => $message,
            'details' => $detailLines,
            'group_id' => $room->id,
        ];
    }

    private function buildPrivateGroupInviteSuccessModalPayload(GroupChatRoom $room, array $summary): array
    {
        return [
            'title' => 'Berhasil Mengundang ' . (int) ($summary['invited_count'] ?? 0) . ' Anggota',
            'group_id' => $room->id,
        ];
    }

    private function hasSuccessfulInviteOutcome(array $summary): bool
    {
        return (($summary['invited_count'] ?? 0) > 0)
            || (($summary['already_member_count'] ?? 0) > 0)
            || (($summary['already_invited_count'] ?? 0) > 0);
    }
}

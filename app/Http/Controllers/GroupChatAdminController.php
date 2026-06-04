<?php

namespace App\Http\Controllers;

use App\Events\GroupChatMessageSent;
use App\Models\GroupChatMember;
use App\Models\GroupChatMessage;
use App\Models\GroupChatRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GroupChatAdminController extends Controller
{
    public function index(Request $request)
    {
        $groupList = $this->resolveAvailableRooms();
        $activeRoom = $this->resolveSelectedRoom($groupList, $request->integer('group'));
        $messages = $activeRoom
            ? $activeRoom->messages
                ->sortBy('created_at')
                ->values()
                ->map(fn (GroupChatMessage $message) => $this->transformMessage($message, $request->user()))
                ->all()
            : [];

        return view('admin.group-chat', [
            'groupList' => $groupList,
            'activeRoom' => $activeRoom,
            'chatPayload' => $activeRoom ? $this->buildChatPayload($activeRoom, $messages) : null,
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
            'messages' => $room->messages
                ->sortBy('created_at')
                ->values()
                ->map(fn (GroupChatMessage $message) => $this->transformMessage($message, $request->user()))
                ->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'pesan' => 'required|string|max:2000',
        ]);

        $room = $this->resolveRoom((int) $validated['group_id']);

        if (! $room) {
            return response()->json([
                'success' => false,
                'message' => 'Grup chat tidak ditemukan.',
            ], 404);
        }

        $message = DB::transaction(function () use ($room, $request, $validated) {
            return GroupChatMessage::create([
                'room_id' => $room->id,
                'user_id' => $request->user()->id,
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
            Log::warning('Broadcast group chat admin gagal dikirim ke websocket.', [
                'message_id' => $message->id,
                'room_id' => $message->room_id,
                'error' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($message, $request->user()),
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

        // Konselor hanya dapat mengubah pesan grup yang dia kirim sendiri.
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

        // Hapus permanen juga dibatasi ke pemilik pesan di grup.
        $message->delete();

        return response()->json([
            'success' => true,
            'deleted_id' => $message->id,
        ]);
    }

    private function resolveAvailableRooms()
    {
        return GroupChatRoom::query()
            ->withCount('members')
            ->with([
                'members.user.profil',
                'latestMessage.sender.profil',
                'latestMessage.sender.mahasiswa',
            ])
            ->where('is_active', true)
            ->get()
            ->sortByDesc(function (GroupChatRoom $room) {
                return optional($room->latestMessage)->created_at?->getTimestamp()
                    ?? optional($room->updated_at)?->getTimestamp()
                    ?? 0;
            })
            ->values();
    }

    private function resolveSelectedRoom($groupList, ?int $roomId): ?GroupChatRoom
    {
        if ($roomId) {
            $room = $groupList->firstWhere('id', $roomId);

            if ($room) {
                $room->loadMissing([
                    'members.user.profil',
                    'members.user.mahasiswa',
                    'messages.sender.profil',
                    'messages.sender.mahasiswa',
                ]);

                return $room;
            }
        }

        $room = $groupList->first();

        if ($room) {
            $room->loadMissing([
                'members.user.profil',
                'members.user.mahasiswa',
                'messages.sender.profil',
                'messages.sender.mahasiswa',
            ]);
        }

        return $room;
    }

    private function resolveRoom(?int $roomId): ?GroupChatRoom
    {
        if (! $roomId) {
            return null;
        }

        return GroupChatRoom::query()
            ->withCount('members')
            ->with([
                'members.user.profil',
                'members.user.mahasiswa',
                'messages.sender.profil',
                'messages.sender.mahasiswa',
            ])
            ->whereKey($roomId)
            ->where('is_active', true)
            ->first();
    }

    private function buildChatPayload(GroupChatRoom $room, array $messages): array
    {
        return [
            'roomId' => $room->id,
            'channel' => 'chat.group.'.$room->id,
            'sendUrl' => route('admin.group-chat.store'),
            'messagesUrl' => route('admin.group-chat.messages'),
            'updateUrlTemplate' => route('admin.group-chat.update', ['message' => '__MESSAGE_ID__']),
            'deleteUrlTemplate' => route('admin.group-chat.destroy', ['message' => '__MESSAGE_ID__']),
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
            return 'Anonim';
        }

        // Di grup chat, mahasiswa selalu tampil anonim dengan nama hewan.
        if ($user->role === 'mahasiswa') {
            return method_exists($user, 'getAnonimDisplayName')
                ? $user->getAnonimDisplayName()
                : 'Mahasiswa Anonim';
        }

        return $user->nama ?? $user->name ?? 'Admin';
    }

    private function resolveUserAvatarUrl(?User $user): string
    {
        if (! $user) {
            return asset('img/default-avatar.png');
        }

        // Di grup chat, mahasiswa selalu pakai avatar anonim hewan.
        if ($user->role === 'mahasiswa') {
            return method_exists($user, 'getAnonimAvatarSvg')
                ? $user->getAnonimAvatarSvg()
                : asset('img/default-avatar.png');
        }

        $profilePhoto = optional($user->profil)->foto;

        return $profilePhoto
            ? Storage::url($profilePhoto)
            : asset('img/default-avatar.png');
    }

    private function resolveMemberNames(GroupChatRoom $room): array
    {
        $members = $room->members;

        $memberNames = $members
            ->sortBy(fn (GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(fn (GroupChatMember $member) => $this->resolveUserDisplayName($member->user))
            ->filter()
            ->values();

        if ($memberNames->isNotEmpty() || (int) ($room->members_count ?? 0) === 0) {
            return $memberNames->all();
        }

        // Fallback langsung dari tabel anggota menjaga dropdown tetap terisi jika relasi belum stabil.
        return GroupChatMember::query()
            ->with('user.mahasiswa')
            ->where('room_id', $room->id)
            ->get()
            ->sortBy(fn (GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(fn (GroupChatMember $member) => $this->resolveUserDisplayName($member->user))
            ->filter()
            ->values()
            ->all();
    }

    private function resolveMemberProfiles(GroupChatRoom $room): array
    {
        $members = $room->members;

        if ($members->isEmpty() && (int) ($room->members_count ?? 0) > 0) {
            // Fallback langsung dari tabel anggota menjaga foto dan nama tetap tersedia saat relasi belum stabil.
            $members = GroupChatMember::query()
                ->with(['user.mahasiswa', 'user.profil'])
                ->where('room_id', $room->id)
                ->get();
        }

        return $members
            ->sortBy(fn (GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(fn (GroupChatMember $member) => [
                'name' => $this->resolveUserDisplayName($member->user),
                'avatar_url' => $this->resolveUserAvatarUrl($member->user),
            ])
            ->filter(fn (array $member) => filled($member['name']))
            ->values()
            ->all();
    }

    private function transformMessage(GroupChatMessage $message, User $viewer): array
    {
        $message->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
        ]);

        $sender = $message->sender;

        return [
            'id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->user_id,
            'sender_name' => $message->user_id === $viewer->id ? 'Anda' : $this->resolveUserDisplayName($sender),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $this->resolveUserAvatarUrl($sender),
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
        $message->loadMissing('room');

        $room = $message->room;

        if (! $room || ! $room->is_active) {
            return null;
        }

        return $user->role === 'konselor' ? $room : null;
    }
}

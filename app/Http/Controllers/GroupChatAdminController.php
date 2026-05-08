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
            'roomTitle' => $room->title,
            'topicLabel' => $room->topicLabel(),
            'memberCount' => (int) ($room->members_count ?? $room->members->count()),
            'memberNames' => $room->members
                ->map(fn (GroupChatMember $member) => $member->user?->getNamaDisplay())
                ->filter()
                ->values()
                ->all(),
            'messages' => $messages,
        ];
    }

    private function transformMessage(GroupChatMessage $message, User $viewer): array
    {
        $message->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
        ]);

        $sender = $message->sender;
        $profil = optional($sender)->profil;

        return [
            'id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->user_id,
            'sender_name' => $message->user_id === $viewer->id ? 'Anda' : ($sender?->getNamaDisplay() ?? 'Pengguna'),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $profil?->foto ? Storage::url($profil->foto) : asset('img/default-avatar.png'),
            'text' => $message->pesan,
            'time' => $this->toDisplayDateTime($message->created_at)?->format('H:i') ?? $this->nowInDisplayTimezone()->format('H:i'),
            'sent_at' => $this->toDisplayDateTime($message->created_at)?->toIso8601String() ?? $this->nowInDisplayTimezone()->toIso8601String(),
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
}

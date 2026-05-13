<?php

namespace App\Http\Controllers;

use App\Events\GroupChatMessageSent;
use App\Models\GroupChatMember;
use App\Models\GroupChatMessage;
use App\Models\GroupChatRoom;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        ]);
    }

    public function create()
    {
        // Halaman terpisah untuk membuat grup baru berdasarkan topik konseling.
        return view('Pages.group-chat-create', [
            'topicOptions' => GroupChatRoom::topicOptions(),
        ]);
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

    public function join(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['nullable', 'integer'],
            'topic' => ['nullable', 'string', Rule::in(array_keys(GroupChatRoom::topicOptions()))],
        ]);

        $user = $request->user();
        $roomId = (int) ($validated['room_id'] ?? 0);
        $topic = $validated['topic'] ?? null;

        if (! $roomId && ! $topic) {
            return back()
                ->withErrors(['topic' => 'Pilih grup yang tersedia atau tentukan topik baru terlebih dahulu.'])
                ->withInput();
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

            $member = DB::transaction(function () use ($room, $user) {
                return GroupChatMember::query()->firstOrCreate(
                    [
                        'room_id' => $room->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'joined_at' => $this->nowInDisplayTimezone(),
                    ]
                );
            });
        } else {
            // Jika topik belum punya room aktif, sistem membuat room baru lalu langsung join.
            $topic = (string) $topic;
            $topicLabel = GroupChatRoom::topicOptions()[$topic];

            [$room, $member] = DB::transaction(function () use ($topic, $topicLabel, $user) {
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

                return [$room, $member];
            });
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
            ->withCount('members')
            ->with([
                'members.user.profil',
                'latestMessage.sender.profil',
                'latestMessage.sender.mahasiswa',
            ])
            ->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
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
            ->withCount('members')
            ->whereKey($roomId)
            ->where('is_active', true)
            ->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
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
            return 'Pengguna';
        }

        if ($user->isAnonim()) {
            return 'Mahasiswa Anonim';
        }

        $nim = optional($user->mahasiswa)->nim;
        static $studentNameCache = [];
        $studentName = $nim
            ? ($studentNameCache[$nim] ??= Student::query()->where('nim', $nim)->value('name'))
            : null;

        return $studentName ?: ($user->nama ?: 'Pengguna');
    }

    private function resolveUserAvatarUrl(?User $user): string
    {
        $profilePhoto = optional($user?->profil)->foto;

        return $profilePhoto ? Storage::url($profilePhoto) : asset('img/default-avatar.png');
    }

    private function resolveMemberNames(GroupChatRoom $room): array
    {
        $members = $room->members;

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

        if ($members->isEmpty() && (int) ($room->members_count ?? 0) > 0) {
            // Fallback langsung dari tabel anggota menjaga foto dan nama tetap tersedia saat relasi belum stabil.
            $members = GroupChatMember::query()
                ->with(['user.mahasiswa', 'user.profil'])
                ->where('room_id', $room->id)
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
            'sender.profil',
            'sender.mahasiswa',
        ]);

        $sender = $message->sender;
        $profil = optional($sender)->profil;

        return [
            'id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->user_id,
            'sender_name' => $message->user_id === $viewer->id ? 'Anda' : $this->resolveUserDisplayName($sender),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $profil?->foto ? Storage::url($profil->foto) : asset('img/default-avatar.png'),
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

        $isMember = $room->members->contains(fn (GroupChatMember $member) => (int) $member->user_id === (int) $user->id);

        return $isMember ? $room : null;
    }
}

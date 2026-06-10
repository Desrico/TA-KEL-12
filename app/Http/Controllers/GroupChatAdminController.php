<?php

namespace App\Http\Controllers;

use App\Events\GroupChatMessageSent;
use App\Models\GroupChatMember;
use App\Models\GroupChatMessage;
use App\Models\GroupChatRoom;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
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
use Illuminate\Support\Str;

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
            ->map(fn(GroupChatMessage $message) => $this->transformMessage($message, $request->user(), $activeRoom))
            ->all()
            : [];

        return view('admin.group-chat', [
            'groupList' => $groupList,
            'activeRoom' => $activeRoom,
            'chatPayload' => $activeRoom ? $this->buildChatPayload($activeRoom, $messages) : null,
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

        $cacheKey = 'admin.group_chat.student_lookup.' . $nim . '.' . $limit;
        $cachedPayload = Cache::get($cacheKey);

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
            $autocompleteTimeout
        );

        if ($items !== [] && $message === null) {
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
        if (! GroupChatSupport::supportsPrivateGroups()) {
            return back()->with('error', 'Grup privat membutuhkan migration database group chat terbaru sebelum dapat digunakan.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'invite_nims' => ['nullable', 'string'],
        ]);

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
            ->with('admin_success_modal', $this->buildPrivateGroupSuccessModalPayload(
                $group,
                'Undangan untuk grup privat "' . $group->title . '" berhasil diproses.',
                $inviteSummary
            ));
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
                ->map(fn(GroupChatMessage $message) => $this->transformMessage($message, $request->user(), $room))
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

        $message->delete();

        return response()->json([
            'success' => true,
            'deleted_id' => $message->id,
        ]);
    }

    private function resolveAvailableRooms()
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
                    'members as invited_members_count' => fn($query) => $query->where('membership_status', GroupChatMember::STATUS_INVITED),
                ])
                ->with([
                    'members' => fn($query) => $query
                        ->where('membership_status', GroupChatMember::STATUS_ACTIVE)
                        ->with('user.mahasiswa'),
                ]);
        } else {
            $roomsQuery
                ->withCount('members')
                ->with([
                    'members.user.mahasiswa',
                ]);
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

    private function resolveSelectedRoom($groupList, ?int $roomId): ?GroupChatRoom
    {
        if ($roomId) {
            $room = $groupList->firstWhere('id', $roomId);

            if ($room) {
                $room->loadMissing([
                    'messages.sender.mahasiswa',
                ]);

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
            $room->loadMissing([
                'messages.sender.mahasiswa',
            ]);

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
            ->with([
                'messages.sender.mahasiswa',
            ])
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

    private function buildChatPayload(GroupChatRoom $room, array $messages): array
    {
        return [
            'roomId' => $room->id,
            'channel' => 'chat.group.' . $room->id,
            'sendUrl' => route('admin.group-chat.store'),
            'messagesUrl' => route('admin.group-chat.messages'),
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
            'memberProfiles' => $this->resolveMemberProfiles($room),
            'inviteUrl' => GroupChatSupport::supportsPrivateGroups() && $room->isPrivate() && filled($room->invite_token)
                ? route('mahasiswa.group-chat.invite', ['token' => $room->invite_token])
                : null,
            'canInviteMembers' => GroupChatSupport::supportsPrivateGroups() && $room->isPrivate(),
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
        return $room->members
            ->sortBy(fn(GroupChatMember $member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
            ->map(function (GroupChatMember $member) use ($room) {
                return [
                    'name' => GroupChatSupport::resolveDisplayName($member->user, $room, $member),
                    'avatar_url' => GroupChatSupport::resolveAvatarUrl(),
                ];
            })
            ->filter(fn(array $member) => filled($member['name']))
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
            'sender_name' => $message->user_id === $viewer->id
                ? 'Anda'
                : GroupChatSupport::resolveDisplayName($sender, $room, $membership),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $this->resolveUserAvatarUrl($sender),
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
        ?int $timeout = null
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

                $message = 'Token statis CIS tidak valid atau sudah kedaluwarsa, sistem memakai data lokal aplikasi.';
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

        $directory = $this->buildStudentDirectory($keyword, $cisRows, $limit);
        $nimOrder = array_keys($directory);

        if ($nimOrder === []) {
            return [[], $source, $message];
        }

        $invitableStudents = $this->resolveInvitableMahasiswaByNims($nimOrder);

        $items = collect($nimOrder)
            ->filter(function (string $nim) use ($directory, $invitableStudents) {
                $source = $directory[$nim]['source'] ?? 'local';

                return $source === 'cis' || $invitableStudents->has($nim);
            })
            ->map(function (string $nim) use ($directory) {
                $directoryItem = $directory[$nim] ?? [];
                $displayName = trim((string) (
                    $directoryItem['name']
                    ?? 'Mahasiswa'
                ));

                return [
                    'nim' => $nim,
                    'name' => $displayName !== '' ? $displayName : 'Mahasiswa',
                    'label' => $nim . ' - ' . ($displayName !== '' ? $displayName : 'Mahasiswa'),
                    'source' => $directoryItem['source'] ?? 'cis',
                ];
            })
            ->filter()
            ->take($limit)
            ->values()
            ->all();

        if ($items === [] && $message === null) {
            $message = 'Data mahasiswa ditemukan, tetapi belum ada akun mahasiswa aktif yang dapat menerima undangan grup privat.';
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

    private function resolveInvitableMahasiswaByNims(array $nims)
    {
        return Mahasiswa::query()
            ->with('user:id,nama,role')
            ->whereIn('nim', $nims)
            ->get(['id', 'user_id', 'nim'])
            ->filter(fn(Mahasiswa $mahasiswa) => $mahasiswa->user && $mahasiswa->user->role === 'mahasiswa')
            ->keyBy(fn(Mahasiswa $mahasiswa) => (string) $mahasiswa->nim);
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
                'missing_nims' => [],
                'unavailable_nims' => [],
                'blocked_nims' => [],
            ];
        }

        $this->syncMahasiswaFromCisByNims($nims);

        $students = Mahasiswa::query()
            ->with('user')
            ->whereIn('nim', $nims)
            ->get()
            ->keyBy(fn(Mahasiswa $mahasiswa) => (string) $mahasiswa->nim);

        $invitedCount = 0;
        $alreadyMemberCount = 0;
        $blockedNims = [];
        $unavailableNims = [];

        foreach ($nims as $nim) {
            /** @var Mahasiswa|null $mahasiswa */
            $mahasiswa = $students->get($nim);
            $user = $mahasiswa?->user;

            if (! $user || $user->role !== 'mahasiswa') {
                $unavailableNims[] = $nim;
                continue;
            }

            $eligibility = GroupChatSupport::resolveAcademicEligibility($user);
            if (! $eligibility['eligible']) {
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

            if ($member->exists && GroupChatSupport::supportsMembershipStatus() && in_array($member->membership_status, [
                GroupChatMember::STATUS_BLOCKED,
                GroupChatMember::STATUS_REMOVED,
            ], true) && $member->removed_reason !== 'academic_inactive') {
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
            'missing_nims' => $missingNims,
            'unavailable_nims' => $unavailableNims,
            'blocked_nims' => $blockedNims,
        ];
    }

    private function syncMahasiswaFromCisByNims(array $nims): void
    {
        $missingNims = array_values(array_diff($nims, Mahasiswa::query()->whereIn('nim', $nims)->pluck('nim')->all()));

        if ($missingNims === []) {
            return;
        }

        /** @var KampusApiService $kampusApi */
        $kampusApi = app(KampusApiService::class);

        foreach ($missingNims as $nim) {
            try {
                $cisRow = $kampusApi->findActiveMahasiswaByExactNim($nim);

                if (! $cisRow || trim((string) ($cisRow['nim'] ?? '')) === '') {
                    continue;
                }

                $this->syncMahasiswaFromCisRow($cisRow);
            } catch (\Throwable $exception) {
                Log::warning('Sinkronisasi mahasiswa CIS untuk undangan group chat gagal.', [
                    'nim' => $nim,
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

        return Mahasiswa::query()->with('user')->where('nim', $nim)->first();
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

        if ($summary['missing_nims'] !== []) {
            $parts[] = 'NIM tidak ditemukan: ' . implode(', ', $summary['missing_nims']) . '.';
        }

        if (($summary['unavailable_nims'] ?? []) !== []) {
            $parts[] = 'Data ditemukan, tetapi akun mahasiswa aktif untuk menerima undangan belum tersedia: ' . implode(', ', $summary['unavailable_nims']) . '.';
        }

        if ($summary['blocked_nims'] !== []) {
            $parts[] = 'Beberapa undangan dilewati karena status anggota dibatasi: ' . implode(', ', $summary['blocked_nims']) . '.';
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

        if (! empty($summary['missing_nims'])) {
            $detailLines[] = 'NIM tidak ditemukan: ' . implode(', ', $summary['missing_nims']) . '.';
        }

        if (! empty($summary['unavailable_nims'])) {
            $detailLines[] = 'Data ditemukan, tetapi akun mahasiswa aktif untuk menerima undangan belum tersedia: ' . implode(', ', $summary['unavailable_nims']) . '.';
        }

        if (! empty($summary['blocked_nims'])) {
            $detailLines[] = 'Undangan dilewati untuk status anggota terbatas: ' . implode(', ', $summary['blocked_nims']) . '.';
        }

        return [
            'title' => 'Grup Privat Berhasil',
            'message' => $message,
            'details' => $detailLines,
            'group_id' => $room->id,
        ];
    }

    private function hasSuccessfulInviteOutcome(array $summary): bool
    {
        return (($summary['invited_count'] ?? 0) > 0)
            || (($summary['already_member_count'] ?? 0) > 0);
    }
}

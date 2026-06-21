<?php

namespace App\Support;

use App\Models\GroupChatMember;
use App\Models\GroupChatRoom;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupChatSupport
{
    public const CONSENT_VERSION = 'group-chat-v1';
    public const PRIVATE_GROUP_MEMBER_LIMIT = 12;
    public const SESSION_RESET_HOURS = 168;
    private static array $schemaSupportCache = [];

    public static function consentVersion(): string
    {
        return self::CONSENT_VERSION;
    }

    public static function rules(): array
    {
        return [
            'Gunakan bahasa yang sopan, baik, dan saling menghormati.',
            'Dilarang spam, flooding, atau mengirim pesan berulang yang tidak relevan.',
            'Hindari membagikan data pribadi sensitif seperti nomor telepon, alamat, atau kata sandi.',
            'Fokuskan percakapan pada topik grup dan kebutuhan konseling yang relevan.',
            'Konselor dapat meninjau percakapan dan mengeluarkan anggota jika aturan dilanggar.',
        ];
    }

    public static function topicDescriptions(): array
    {
        return [
            'akademik' => 'Ruang aman untuk membahas tekanan akademik, tugas, nilai, dan ritme belajar.',
            'kehidupan_kampus' => 'Tempat berbagi pengalaman adaptasi, dinamika organisasi, dan kehidupan sehari-hari di kampus.',
            'intrapersonal' => 'Diskusi tentang emosi, motivasi, rasa cemas, kelelahan, dan refleksi diri.',
            'keluarga' => 'Ruang untuk membahas hubungan keluarga, ekspektasi, dan tekanan dari rumah.',
            'masalah_asrama' => 'Percakapan seputar relasi di asrama, kenyamanan tinggal, dan konflik keseharian.',
            'relasi' => 'Tempat membahas relasi dengan teman, pasangan, atau lingkungan sosial yang dekat.',
            'lainnya' => 'Gunakan opsi ini untuk kebutuhan yang belum cocok dengan topik grup lainnya.',
        ];
    }

    public static function topicDescription(?string $topic): string
    {
        return self::topicDescriptions()[$topic] ?? 'Ruang diskusi kelompok dengan identitas anonim untuk mahasiswa.';
    }

    public static function anonymousNamePool(): array
    {
        return AnonymousIdentitySupport::namePool();
    }

    public static function makePrivateTopicKey(): string
    {
        return 'private_' . Str::lower(Str::random(18));
    }

    public static function supportsMembershipStatus(): bool
    {
        return self::hasColumn('group_chat_members', 'membership_status');
    }

    public static function supportsAnonymousName(): bool
    {
        return self::hasColumn('group_chat_members', 'anonymous_name');
    }

    public static function supportsRoomVisibility(): bool
    {
        return self::hasColumn('group_chat_rooms', 'visibility');
    }

    public static function supportsInviteToken(): bool
    {
        return self::hasColumn('group_chat_rooms', 'invite_token');
    }

    public static function supportsPrivateGroups(): bool
    {
        return self::supportsRoomVisibility() && self::supportsInviteToken() && self::supportsMembershipStatus();
    }

    public static function supportsRoomAvatar(): bool
    {
        return self::hasColumn('group_chat_rooms', 'avatar_path');
    }

    public static function supportsConsentTracking(): bool
    {
        return self::hasColumn('group_chat_members', 'consented_at')
            && self::hasColumn('group_chat_members', 'consent_version');
    }

    public static function supportsMembershipLifecycleFields(): bool
    {
        return self::supportsMembershipStatus()
            && self::hasColumn('group_chat_members', 'joined_via')
            && self::hasColumn('group_chat_members', 'removed_reason')
            && self::hasColumn('group_chat_members', 'removed_at');
    }

    public static function supportsNotificationCtas(): bool
    {
        return self::hasColumn('notifikasi', 'cta_target') && self::hasColumn('notifikasi', 'cta_label');
    }

    public static function supportsSystemMessages(): bool
    {
        return self::hasColumn('group_chat_messages', 'is_system')
            && self::hasColumn('group_chat_messages', 'system_event');
    }

    public static function privateGroupMemberLimit(): int
    {
        return self::PRIVATE_GROUP_MEMBER_LIMIT;
    }

    public static function sessionResetHours(): int
    {
        return self::SESSION_RESET_HOURS;
    }

    public static function roomUsesSessionReset(?GroupChatRoom $room): bool
    {
        if (! $room) {
            return false;
        }

        if (! self::supportsRoomVisibility()) {
            return true;
        }

        return $room->isPrivate();
    }

    public static function currentSessionStartedAt(GroupChatRoom $room, ?Carbon $reference = null): Carbon
    {
        if (! self::roomUsesSessionReset($room)) {
            return ($room->created_at instanceof Carbon ? $room->created_at->copy() : Carbon::parse($room->created_at ?? now()))->startOfSecond();
        }

        $reference ??= now();
        $anchor = ($room->created_at instanceof Carbon ? $room->created_at->copy() : Carbon::parse($room->created_at ?? now()))->startOfSecond();
        $current = ($reference instanceof Carbon ? $reference->copy() : Carbon::parse($reference))->startOfSecond();
        $hoursElapsed = max(0, $anchor->diffInHours($current));
        $hoursPerSession = max(1, self::sessionResetHours());
        $completedSessions = intdiv($hoursElapsed, $hoursPerSession);

        return $anchor->addHours($completedSessions * $hoursPerSession);
    }

    public static function ensureMemberAlias(GroupChatRoom $room, GroupChatMember $member): GroupChatMember
    {
        if (filled($member->anonymous_name)) {
            return $member;
        }

        $member->anonymous_name = self::nextAvailableAlias($room, $member);

        if (! self::supportsAnonymousName()) {
            return $member;
        }

        $member->save();

        return $member->refresh();
    }

    // Grup publik mempertahankan identitas anonim, sedangkan grup privat memakai nama asli.
    public static function roomUsesAnonymousIdentity(?GroupChatRoom $room): bool
    {
        if (! $room) {
            return false;
        }

        if (! self::supportsRoomVisibility()) {
            return true;
        }

        return $room->isPublic();
    }

    public static function resolveDisplayName(?User $user, ?GroupChatRoom $room = null, ?GroupChatMember $member = null): string
    {
        if (! $user) {
            return 'Pengguna';
        }

        if ($user->role === 'konselor') {
            return 'Konselor';
        }

        // Mahasiswa hanya memakai alias hewan saat berada di grup publik.
        if ($room && $user->role === 'mahasiswa') {
            if (! self::roomUsesAnonymousIdentity($room)) {
                return $user->nama ?: 'Mahasiswa';
            }

            $member ??= self::resolveRoomMember($user, $room);

            if ($member) {
                if (! filled($member->anonymous_name)) {
                    $member = self::ensureMemberAlias($room, $member);
                }

                return $member->anonymous_name ?: 'Mahasiswa Anonim';
            }

            return self::buildDeterministicAlias($room->id, $user->id);
        }

        $nim = optional($user->mahasiswa)->nim;
        static $studentNameCache = [];
        $studentName = $nim
            ? ($studentNameCache[$nim] ??= Student::query()->where('nim', $nim)->value('name'))
            : null;

        return $studentName ?: ($user->nama ?: 'Pengguna');
    }

    public static function resolveAvatarUrl(): string
    {
        // Semua avatar group chat dibuat generik agar foto profil asli tidak membocorkan identitas.
        return asset('img/default-avatar.png');
    }

    public static function resolveRoomAvatarUrl(?GroupChatRoom $room): ?string
    {
        if (! $room || ! self::supportsRoomAvatar()) {
            return null;
        }

        $path = trim((string) ($room->avatar_path ?? ''));

        if ($path === '') {
            return null;
        }

        return Storage::url($path);
    }

    public static function resolveRoomAvatarInitial(?GroupChatRoom $room): string
    {
        $label = trim((string) ($room?->title ?: $room?->topicLabel() ?: 'G'));

        return Str::upper(Str::substr($label, 0, 1) ?: 'G');
    }

    public static function resolveAcademicEligibility(User $user): array
    {
        if ($user->role !== 'mahasiswa') {
            return ['eligible' => true, 'reason' => null];
        }

        $nim = optional($user->mahasiswa)->nim;
        if (! $nim) {
            return ['eligible' => true, 'reason' => null];
        }

        $student = Student::query()->where('nim', $nim)->first();
        if (! $student) {
            return ['eligible' => true, 'reason' => null];
        }

        // Hook ini sengaja lentur agar siap dipakai saat sinkronisasi status resmi dari CIS/API kampus tersedia.
        $statusCandidates = [
            data_get($student, 'status_akademik'),
            data_get($student, 'status_mahasiswa'),
            data_get($student, 'academic_status'),
            data_get($student, 'status'),
        ];

        foreach ($statusCandidates as $candidate) {
            $normalizedStatus = Str::lower(trim((string) $candidate));

            if (in_array($normalizedStatus, ['lulus', 'alumni', 'nonaktif', 'inactive', 'graduated'], true)) {
                return [
                    'eligible' => false,
                    'reason' => 'Akses group chat tidak tersedia lagi karena status akademik Anda sudah nonaktif atau lulus.',
                ];
            }
        }

        $activeFlags = [
            data_get($student, 'is_active_student'),
            data_get($student, 'is_active'),
            data_get($student, 'active'),
        ];

        foreach ($activeFlags as $flag) {
            if ($flag !== null && ! filter_var($flag, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE)) {
                return [
                    'eligible' => false,
                    'reason' => 'Akses group chat tidak tersedia lagi karena status akademik Anda sudah nonaktif.',
                ];
            }
        }

        $graduatedAt = data_get($student, 'graduated_at') ?: data_get($student, 'tanggal_lulus');
        if ($graduatedAt) {
            try {
                if (Carbon::parse($graduatedAt)->lessThanOrEqualTo(now())) {
                    return [
                        'eligible' => false,
                        'reason' => 'Akses group chat tidak tersedia lagi karena status akademik Anda sudah lulus.',
                    ];
                }
            } catch (\Throwable) {
                // Jika format tanggal dari sumber sinkronisasi belum stabil, akses tidak langsung diblokir.
            }
        }

        return ['eligible' => true, 'reason' => null];
    }

    public static function syncMemberEligibilityStatus(User $user): array
    {
        $eligibility = self::resolveAcademicEligibility($user);

        if (! $eligibility['eligible'] && self::supportsMembershipLifecycleFields()) {
            GroupChatMember::query()
                ->where('user_id', $user->id)
                ->whereIn('membership_status', [
                    GroupChatMember::STATUS_ACTIVE,
                    GroupChatMember::STATUS_INVITED,
                ])
                ->update([
                    'membership_status' => GroupChatMember::STATUS_BLOCKED,
                    'removed_reason' => 'academic_inactive',
                    'removed_at' => now(),
                ]);
        }

        return $eligibility;
    }

    public static function resolveRoomMember(User $user, GroupChatRoom $room): ?GroupChatMember
    {
        if ($room->relationLoaded('members')) {
            $member = $room->members->first(fn (GroupChatMember $item) => (int) $item->user_id === (int) $user->id);
            if ($member) {
                return $member;
            }
        }

        return GroupChatMember::query()
            ->where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();
    }

    private static function nextAvailableAlias(GroupChatRoom $room, GroupChatMember $member): string
    {
        if (! self::supportsAnonymousName() || ! self::supportsMembershipStatus()) {
            return self::buildDeterministicAlias($room->id, $member->user_id ?: $member->id ?: 0);
        }

        $usedAliases = GroupChatMember::query()
            ->where('room_id', $room->id)
            ->when($member->id, fn ($query) => $query->whereKeyNot($member->id))
            ->whereIn('membership_status', [
                GroupChatMember::STATUS_ACTIVE,
                GroupChatMember::STATUS_INVITED,
                GroupChatMember::STATUS_BLOCKED,
            ])
            ->whereNotNull('anonymous_name')
            ->pluck('anonymous_name')
            ->map(fn ($alias) => trim((string) $alias))
            ->filter()
            ->values()
            ->all();

        $usedLookup = array_flip($usedAliases);

        foreach (self::anonymousNamePool() as $alias) {
            if (! isset($usedLookup[$alias])) {
                return $alias;
            }
        }

        foreach (self::anonymousNamePool() as $alias) {
            for ($number = 2; $number <= 999; $number++) {
                $candidate = $alias . ' ' . $number;
                if (! isset($usedLookup[$candidate])) {
                    return $candidate;
                }
            }
        }

        return 'Anonim ' . Str::upper(Str::random(4));
    }

    private static function buildDeterministicAlias(int $roomId, int|string|null $memberSeed): string
    {
        $seed = (string) ($memberSeed ?: '0');
        $hash = hexdec(substr(hash('crc32b', $roomId . ':' . $seed), 0, 7));
        $pool = self::anonymousNamePool();
        $poolCount = count($pool);
        $index = $poolCount > 0 ? $hash % $poolCount : 0;
        $cycle = $poolCount > 0 ? intdiv($hash, $poolCount) : 0;
        $alias = $pool[$index] ?? 'Mahasiswa Anonim';

        if ($cycle > 0) {
            $alias .= ' ' . (($cycle % 9) + 2);
        }

        return $alias;
    }

    private static function hasColumn(string $table, string $column): bool
    {
        $cacheKey = $table . '.' . $column;

        if (array_key_exists($cacheKey, self::$schemaSupportCache)) {
            return self::$schemaSupportCache[$cacheKey];
        }

        try {
            return self::$schemaSupportCache[$cacheKey] = Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return self::$schemaSupportCache[$cacheKey] = false;
        }
    }
}

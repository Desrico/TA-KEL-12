<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\JadwalKonseling;
use App\Models\SesiKonseling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $konselor = \App\Models\Konselor::where('user_id', Auth::id())->first();

        $jadwalList = JadwalKonseling::with(['mahasiswa.user', 'sesiKonseling.chatMessages'])
            ->when($konselor, function ($q) use ($konselor) {
                $q->where('konselor_id', $konselor->id);
            })
            ->where('jenis', 'online')
            ->whereIn('status', ['disetujui', 'berlangsung'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->get();

        $activeJadwal = null;

        if ($request->filled('jadwal')) {
            $activeJadwal = JadwalKonseling::with(['mahasiswa.user', 'sesiKonseling.chatMessages'])
                ->when($konselor, function ($q) use ($konselor) {
                    $q->where('konselor_id', $konselor->id);
                })
                ->where('jenis', 'online')
                ->whereIn('status', ['disetujui', 'berlangsung'])
                ->find($request->jadwal);
        }

        $activeSession = $activeJadwal?->sesiKonseling;

        $scheduledStart = null;
        if ($activeJadwal) {
            $scheduledStart = \Carbon\Carbon::parse(
                $activeJadwal->tanggal . ' ' . $activeJadwal->waktu,
                'Asia/Jakarta'
            );
        }

        $canStartNow = $scheduledStart
            ? now('Asia/Jakarta')->greaterThanOrEqualTo($scheduledStart)
            : false;

        $isBlockedBySchedule = $scheduledStart
            ? now('Asia/Jakarta')->lt($scheduledStart)
            : false;

        $isReadyToStart = $activeJadwal && $activeJadwal->status === 'disetujui';
        $chatAccessGranted = $activeJadwal && $activeJadwal->status === 'berlangsung';

        $scheduledStartLabel = $scheduledStart
            ? $scheduledStart->translatedFormat('j F Y, H:i') . ' WIB'
            : '-';

        $chatPayload = null;

        return view('admin.chat', compact(
            'jadwalList',
            'activeJadwal',
            'activeSession',
            'chatPayload',
            'isReadyToStart',
            'isBlockedBySchedule',
            'chatAccessGranted',
            'canStartNow',
            'scheduledStartLabel'
        ));
    }

    public function session($sessionId)
    {
        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])->findOrFail($sessionId);
        $sesi = $this->resolveSesi($jadwal);
        $mahasiswa = $jadwal->mahasiswa;
        $user = $mahasiswa?->user;
        $konselorUser = $jadwal->konselor?->user;

        $topik = $jadwal->topik ?? null;

        if (!$topik && !empty($jadwal->catatan)) {
            if (preg_match('/Topik:\s*([^|]+)/i', $jadwal->catatan, $match)) {
                $topik = trim($match[1]);
            }
        }

        $sessionData = [
            'id' => $jadwal->id,
            'sesi_id' => $sesi->id,
            'nim' => $mahasiswa->nim ?? '-',
            'nama' => $user->nama ?? '-',
            'prodi' => $mahasiswa->jurusan ?? '-',
            'topik' => $topik ?? '-',
            'jenis' => ucfirst($jadwal->jenis ?? 'Online'),
            'tanggal' => $jadwal->tanggal ? \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y') : '-',
            'waktu' => $jadwal->waktu ? substr($jadwal->waktu, 0, 5) : '-',
            'konselor' => $konselorUser->nama ?? 'Konselor',
        ];

        $messages = Chat::with('pengirim')
            ->where('sesi_id', $sesi->id)
            ->orderBy('created_at')
            ->get();

        $isReadyToStart = $jadwal->status === 'disetujui';

        return view('admin.chat', [
            'activeJadwal' => $jadwal,
            'sessionData' => $sessionData,
            'messages' => $messages,
            'isReadyToStart' => $isReadyToStart,
            'isBlockedBySchedule' => false,
            'chatAccessGranted' => false,
        ]);
    }

    public function store(Request $request, $sessionId)
    {
        $request->validate([
            'pesan' => ['required', 'string', 'max:5000'],
        ]);

        $jadwal = JadwalKonseling::findOrFail($sessionId);
        $sesi = $this->resolveSesi($jadwal);

        Chat::create([
            'sesi_id' => $sesi->id,
            'pengirim_id' => Auth::id(),
            'pesan' => $request->pesan,
        ]);

        if ($jadwal->status === 'disetujui') {
            $jadwal->update(['status' => 'berlangsung']);
        }

        return redirect()->route('admin.chat.session', $sessionId);
    }

    public function studentSession($jadwalId)
    {
        $user = Auth::user();
        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])->findOrFail($jadwalId);

        if ($jadwal->mahasiswa?->user?->id !== $user->id) {
            abort(403);
        }

        $sesi = $this->resolveSesi($jadwal);

        $messages = Chat::with('pengirim')
            ->where('sesi_id', $sesi->id)
            ->orderBy('created_at')
            ->get();

        $sessionData = [
            'id' => $jadwal->id,
            'sesi_id' => $sesi->id,
            'nama' => $jadwal->mahasiswa?->user->nama ?? 'Anda',
        ];

        return view('chat.student', compact('sessionData', 'messages'));
    }

    public function studentStore(Request $request, $jadwalId)
    {
        $request->validate([
            'pesan' => ['required', 'string', 'max:5000'],
        ]);

        $user = Auth::user();
        $jadwal = JadwalKonseling::findOrFail($jadwalId);

        if ($jadwal->mahasiswa?->user?->id !== $user->id) {
            abort(403);
        }

        $sesi = $this->resolveSesi($jadwal);

        Chat::create([
            'sesi_id' => $sesi->id,
            'pengirim_id' => Auth::id(),
            'pesan' => $request->pesan,
        ]);

        return redirect()->route('chat.student', $jadwalId);
    }

    private function resolveSesi(JadwalKonseling $jadwal): SesiKonseling
    {
        $jadwalKey = SesiKonseling::jadwalForeignKey();

        $sesi = SesiKonseling::firstOrCreate([
            $jadwalKey => $jadwal->id,
        ]);

        $updates = [];

        if (Schema::hasColumn('sesi_konseling', 'status') && $sesi->status === 'menunggu') {
            $updates['status'] = 'berlangsung';
        }

        if (Schema::hasColumn('sesi_konseling', 'waktu_mulai') && empty($sesi->waktu_mulai)) {
            $updates['waktu_mulai'] = now();
        }

        if ($updates) {
            $sesi->update($updates);
            $sesi->refresh();
        }

        return $sesi;
    }
}

public function mulai($id)
{
    $jadwal = JadwalKonseling::findOrFail($id);

    // contoh redirect (sementara)
    return redirect()->route('riwayat.detail', $id)
        ->with('success', 'Sesi dimulai');
}
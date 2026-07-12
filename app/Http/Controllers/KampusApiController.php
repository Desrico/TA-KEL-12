<?php

namespace App\Http\Controllers;

use App\Services\KampusApiService;
use Illuminate\Http\Request;

class KampusApiController extends Controller
{
    public function mahasiswa(Request $request, KampusApiService $kampusApi)
{
    // Ambil token dari header Authorization jika ada
    $token = str_replace('Bearer ', '', $request->header('Authorization', ''));
    $token = $token ?: null;

    try {
        $result = $kampusApi->getMahasiswa([
            'nama'     => $request->nama ?? '',
            'nim'      => $request->nim ?? '',
            'angkatan' => $request->angkatan ?? '',
            'userid'   => $request->userid ?? '',
            'username' => $request->username ?? '',
            'prodi'    => $request->prodi ?? '',
            'status'   => $request->status ?? 'Aktif',
            'limit'    => $request->limit ?? '',
        ], $token); // ← pass token ke sini

        return response()->json($result);
    } catch (\Throwable $e) {
        return response()->json([
            'result'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function mahasiswaByNim(string $nim, Request $request, KampusApiService $kampusApi)
{
    $token = str_replace('Bearer ', '', $request->header('Authorization', ''));
    $token = $token ?: null;

    try {
        $result = $kampusApi->getStudentByNim($nim, $token); // 

        return response()->json($result);
    } catch (\Throwable $e) {
        return response()->json([
            'result'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
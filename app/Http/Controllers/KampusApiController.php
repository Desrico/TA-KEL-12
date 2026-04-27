<?php

namespace App\Http\Controllers;

use App\Services\KampusApiService;
use Illuminate\Http\Request;

class KampusApiController extends Controller
{
    public function mahasiswa(Request $request, KampusApiService $kampusApi)
    {
        try {
            $result = $kampusApi->getMahasiswa([
                'nama' => $request->nama ?? '',
                'nim' => $request->nim ?? '',
                'angkatan' => $request->angkatan ?? '',
                'userid' => $request->userid ?? '',
                'username' => $request->username ?? '',
                'prodi' => $request->prodi ?? '',
                'status' => $request->status ?? 'Aktif',
                'limit' => $request->limit ?? '',
            ]);

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'result' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function mahasiswaByNim(string $nim, KampusApiService $kampusApi)
    {
        try {
            $result = $kampusApi->getStudentByNim($nim);

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'result' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
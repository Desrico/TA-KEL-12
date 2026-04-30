<?php
use Illuminate\Support\Facades\DB;

echo "Memulai pembersihan collection moods...\n";

$dirtyCount = DB::connection('mongodb')->table('moods')->whereNotNull('user_id')->count();
echo "Ditemukan $dirtyCount data kotor (check-in yang masuk ke collection moods).\n";

if ($dirtyCount > 0) {
    DB::connection('mongodb')->table('moods')->whereNotNull('user_id')->delete();
    echo "Pembersihan berhasil. $dirtyCount data telah dihapus.\n";
} else {
    echo "Tidak ada data kotor yang perlu dihapus.\n";
}

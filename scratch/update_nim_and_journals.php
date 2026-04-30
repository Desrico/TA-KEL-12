<?php
use Illuminate\Support\Facades\DB;

echo "=== UPDATE NIM WHISNU ===\n";
$oldNim = null;
$newNim = '11423045';

$student = DB::connection('mongodb')->table('students')->where('name', 'like', '%whisnu%')->first();
if ($student) {
    $oldNim = $student->nim;
    echo "Ditemukan: {$student->name} dengan NIM lama: {$oldNim}\n";
    
    if ($oldNim !== $newNim) {
        DB::connection('mongodb')->table('students')->where('nim', $oldNim)->update(['nim' => $newNim]);
        echo "NIM di tabel students berhasil diupdate menjadi $newNim.\n";

        $count1 = DB::connection('mongodb')->table('daily_checkins')->where('nim', $oldNim)->update(['nim' => $newNim]);
        echo "Diupdate $count1 record di daily_checkins.\n";

        $count2 = DB::connection('mongodb')->table('journal_texts')->where('nim', $oldNim)->update(['nim' => $newNim]);
        echo "Diupdate $count2 record di journal_texts.\n";
    } else {
        echo "NIM sudah $newNim, tidak ada yang perlu diubah.\n";
    }
} else {
    echo "Mahasiswa bernama whisnu tidak ditemukan.\n";
}

echo "\n=== UPDATE JURNAL DUMMY ===\n";

$realisticJournals = [
    "Hari ini benar-benar melelahkan. Tugas pemrograman sepertinya tidak ada habisnya. Saya sudah menghabiskan waktu berjam-jam untuk men-debug kode tapi masih saja ada error yang tidak masuk akal. Terkadang saya merasa salah memilih jurusan, tapi kalau dipikir-pikir lagi, saya sudah sejauh ini. Mungkin saya hanya butuh tidur yang cukup dan secangkir kopi besok pagi. Semoga besok lebih baik.",
    
    "Belakangan ini saya merasa sedikit cemas dengan presentasi proyek akhir yang semakin dekat. Anggota kelompok saya sulit sekali diajak diskusi dan sering menunda pekerjaan. Tadi malam saya mencoba mengambil alih beberapa bagian, tapi rasanya terlalu berat jika dikerjakan sendiri. Saya mencoba menenangkan diri dengan mendengarkan musik dan berjalan-jalan sebentar di sore hari. Perasaan cemas ini membuat perut saya sedikit mual, tapi saya yakin semuanya akan berlalu jika dikerjakan perlahan.",
    
    "Wah, hari ini cukup menyenangkan! Praktikum basis data berjalan sangat lancar dan saya berhasil menyelesaikan semua modul lebih awal dari teman-teman yang lain. Asisten laboratorium juga memuji pekerjaan saya yang rapi. Tadi siang saya menyempatkan diri makan siang bersama teman-teman lama di kantin, kami banyak tertawa membicarakan hal-hal konyol. Senang rasanya bisa merasa 'hidup' kembali di tengah padatnya jadwal kuliah yang sering membuat stres.",
    
    "Perasaan saya sedang sangat datar akhir-akhir ini. Tidak ada yang istimewa, hari demi hari berlalu dengan rutinitas yang sama: bangun, kuliah, mengerjakan tugas, lalu tidur lagi. Terkadang saya merasa kosong dan kehilangan motivasi. Mungkin karena cuaca yang selalu mendung atau mungkin saya mulai bosan dengan rutinitas ini. Saya berencana mencari kegiatan baru akhir pekan ini, mungkin bergabung dengan UKM atau sekadar mencoba resep masakan baru di kos agar lebih bersemangat.",
    
    "Tadi pagi saya bangun terlambat dan terpaksa melewatkan kuis mata kuliah algoritma. Rasanya kesal dan marah pada diri sendiri karena saya sebenarnya sudah belajar semalaman, tapi justru ketiduran karena alarm tidak bunyi. Dosen mengatakan tidak ada kuis susulan. Saya merasa sangat sedih dan kecewa. Seharian saya jadi tidak mood melakukan apa-apa dan hanya berdiam diri di kamar merutuki kebodohan saya. Harusnya saya tidak memaksakan diri begadang.",
    
    "Saya merasa sangat terkejut sekaligus bersyukur hari ini. Tiba-tiba saya mendapat email bahwa proposal proyek yang saya dan tim ajukan berhasil diterima! Rasanya semua lelah dan begadang berhari-hari terbayar lunas. Saya langsung memberi tahu orang tua dan mereka terdengar sangat bangga. Malam ini kami berencana merayakannya dengan makan malam bersama tim. Momen seperti ini mengingatkan saya mengapa saya tidak boleh menyerah di tengah kesulitan kuliah."
];

$journals = DB::connection('mongodb')->table('journal_texts')->get();
$count = 0;

foreach ($journals as $journal) {
    if (strpos($journal->description, 'Ini adalah catatan jurnal dummy untuk menguji sistem monitoring kesehatan mental mahasiswa.') !== false) {
        $newText = $realisticJournals[array_rand($realisticJournals)];
        
        DB::connection('mongodb')->table('journal_texts')
            ->where('_id', $journal->_id)
            ->update(['description' => $newText]);
        $count++;
    }
}

echo "Berhasil mengganti $count teks jurnal dummy yang aneh dengan catatan jurnal realistis yang lebih panjang.\n";

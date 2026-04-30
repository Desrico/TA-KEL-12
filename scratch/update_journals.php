<?php
use Illuminate\Support\Facades\DB;

$realisticJournals = [
    "Hari ini benar-benar melelahkan. Tugas pemrograman dari dosen sepertinya tidak ada habisnya. Saya sudah menghabiskan waktu berjam-jam untuk men-debug kode tapi masih saja ada error yang tidak masuk akal. Terkadang saya merasa ragu, tapi kalau dipikir-pikir lagi, saya sudah sejauh ini. Mungkin saya hanya butuh istirahat sejenak. Semoga besok otak saya lebih segar untuk melanjutkannya.",
    
    "Belakangan ini saya merasa sedikit tertekan dengan proyek akhir. Anggota kelompok sulit diajak diskusi dan sering menunda pekerjaan. Tadi malam saya mencoba mengambil alih beberapa bagian agar progres jalan, tapi rasanya terlalu berat jika dikerjakan sendiri. Saya mencoba menenangkan diri dengan mendengarkan musik, namun dada ini rasanya masih sesak. Saya harus membicarakannya dengan mereka besok.",
    
    "Wah, hari ini cukup menyenangkan! Praktikum berjalan sangat lancar dan saya berhasil menyelesaikan semua modul lebih awal dari teman-teman. Asisten laboratorium juga memuji pekerjaan saya. Tadi siang saya menyempatkan diri makan siang bersama teman-teman lama di kantin, kami banyak tertawa membicarakan hal-hal konyol. Senang rasanya bisa terlepas sejenak dari stres perkuliahan.",
    
    "Perasaan saya sedang sangat datar akhir-akhir ini. Tidak ada yang istimewa, hari demi hari berlalu dengan rutinitas yang sama: bangun, kuliah, mengerjakan tugas, lalu tidur lagi. Terkadang saya merasa kosong dan kehilangan motivasi. Saya berencana mencari kegiatan baru akhir pekan ini, mungkin bergabung dengan kepanitiaan himpunan atau sekadar mencoba hal baru di kos.",
    
    "Tadi pagi saya bangun terlambat dan terpaksa melewatkan kuis mata kuliah utama. Rasanya kesal dan marah pada diri sendiri karena saya sebenarnya sudah belajar semalaman, tapi justru ketiduran karena alarm tidak bunyi. Dosen mengatakan tidak ada kuis susulan. Saya merasa sangat sedih dan kecewa. Seharian saya jadi tidak mood melakukan apa-apa dan hanya berdiam diri di kamar merutuki kecerobohan saya.",
    
    "Saya merasa sangat terkejut sekaligus bersyukur hari ini. Tiba-tiba saya mendapat pengumuman bahwa saya lolos seleksi beasiswa! Rasanya semua lelah dan perjuangan mempertahankan IPK terbayar lunas. Saya langsung menelepon orang tua dan mereka menangis bahagia. Malam ini kami berencana merayakannya walau hanya sekadar makan malam sederhana. Momen seperti ini mengingatkan saya mengapa saya harus terus maju."
];

$journals = DB::connection('mongodb')->table('journal_texts')->get();
$count = 0;

foreach ($journals as $journal) {
    if (strpos($journal->description, 'Ini adalah catatan jurnal dummy') !== false) {
        $newText = $realisticJournals[array_rand($realisticJournals)];
        
        DB::connection('mongodb')->table('journal_texts')
            ->where('_id', $journal->_id)
            ->update(['description' => $newText]);
        $count++;
    }
}

echo "Berhasil mengganti $count teks jurnal dummy dengan catatan jurnal realistis yang lebih panjang.\n";

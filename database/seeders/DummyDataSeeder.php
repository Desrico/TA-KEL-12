<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\DailyCheckin;
use App\Models\JournalText;
use App\Models\Mood;
use App\Models\Feeling;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $prodis = ['Teknologi Rekayasa Perangkat Lunak', 'Informatika', 'Sistem Informasi Manajemen', 'Teknik Elektro'];
        $genders = ['Laki-laki', 'Perempuan'];
        
        $moods = Mood::all();
        if ($moods->isEmpty()) {
            $moodNames = ['Marah', 'Sedih', 'Takut', 'Jijik', 'Terkejut', 'Biasa', 'Senang'];
            foreach ($moodNames as $index => $name) {
                Mood::create([
                    'mood_name' => $name,
                    'mood_code' => 'M' . ($index + 1)
                ]);
            }
            $moods = Mood::all();
        }

        $feelings = Feeling::all();
        if ($feelings->isEmpty()) {
            $feelingNames = ['Stres', 'Cemas', 'Lelah', 'Bosan', 'Tenang', 'Antusias', 'Bersyukur'];
            foreach ($feelingNames as $index => $name) {
                Feeling::create([
                    'feeling_name' => $name,
                    'feeling_code' => 'F' . ($index + 1)
                ]);
            }
            $feelings = Feeling::all();
        }

        $moodIds = $moods->pluck('_id')->toArray();
        $feelingIds = $feelings->pluck('_id')->toArray();

        $names = [
            'Budi Santoso', 'Siti Aminah', 'Andi Pratama', 'Ayu Lestari', 'Reza Pahlevi',
            'Dewi Sartika', 'Fajar Sidik', 'Rina Wati', 'Hendra Gunawan', 'Maya Sari',
            'Rizky Aditya', 'Dian Sastrowardoyo', 'Arif Rahman', 'Nia Ramadhani', 'Kevin Sanjaya',
            'Dina Mariana', 'Agus Yudhoyono', 'Putri Titian', 'Irfan Bachdim', 'Tika Panggabean'
        ];

        for ($i = 1; $i <= 15; $i++) {
            $nimPrefix = ['113', '114', '115', '133'][rand(0, 3)];
            $nim = $nimPrefix . '230' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $gender = $genders[rand(0, 1)];
            
            // Random mental level and logic
            $mentalLevel = rand(0, 3);
            $mentalLabel = ['Aman', 'Ringan', 'Perlu Pantauan', 'Krisis'][$mentalLevel];
            $mentalRedFlag = $mentalLevel >= 2 ? 'Terdeteksi pola stres berkelanjutan dan penurunan mood drastis.' : null;

            $student = Student::create([
                'nim' => $nim,
                'name' => $names[$i - 1] ?? ('Mahasiswa Dummy ' . $i),
                'gender' => $gender,
                'prodi' => $prodis[rand(0, 3)],
                'password' => bcrypt('password123'),
                'point' => rand(100, 500),
                'energy_score' => rand(50, 100),
                'phone_number' => '0812' . rand(10000000, 99999999),
                'mental_level' => $mentalLevel,
                'mental_label' => $mentalLabel,
                'mental_confidence' => rand(60, 95),
                'mental_red_flag' => $mentalRedFlag,
                'mental_scanned_at' => Carbon::now()->subDays(rand(0, 2)),
            ]);

            // Create Journal Texts
            for ($j = 0; $j < rand(2, 5); $j++) {
                JournalText::create([
                    'nim' => $nim,
                    'description' => 'Ini adalah catatan jurnal dummy untuk menguji sistem monitoring kesehatan mental mahasiswa. Hari ini saya merasa ' . (rand(0, 1) ? 'cukup baik dan produktif.' : 'sedikit lelah dengan tugas kuliah.'),
                    'created_at' => Carbon::now()->subDays(rand(0, 14))->subHours(rand(1, 23))
                ]);
            }

            // Create Daily Checkins (7-14 days)
            $checkinDays = rand(7, 14);
            for ($k = $checkinDays; $k >= 0; $k--) {
                DailyCheckin::create([
                    'nim' => $nim,
                    'mood_id' => $moodIds[array_rand($moodIds)],
                    'feeling_id' => $feelingIds[array_rand($feelingIds)],
                    'created_at' => Carbon::now()->subDays($k)->subHours(rand(1, 10))
                ]);
            }
        }
    }
}

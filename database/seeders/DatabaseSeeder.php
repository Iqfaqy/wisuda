<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wisudawan;
use App\Models\Kursi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'nim' => 'admin001',
            'name' => 'Admin Wisuda',
            'email' => 'admin@unsiq.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // Create sample wisudawan
        $wisudawanData = [
            ['nim' => '2020101', 'name' => 'Budi Santoso', 'email' => 'budi.santoso@example.com', 'jk' => 'L', 'prodi' => 'Teknik Informatika', 'ipk' => 3.65],
            ['nim' => '2020102', 'name' => 'Siti Aminah', 'email' => 'siti.aminah@example.com', 'jk' => 'P', 'prodi' => 'Teknik Informatika', 'ipk' => 3.85],
            ['nim' => '2020103', 'name' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@example.com', 'jk' => 'L', 'prodi' => 'Manajemen', 'ipk' => 3.45],
            ['nim' => '2020104', 'name' => 'Rina Lestari', 'email' => 'rina.lestari@example.com', 'jk' => 'P', 'prodi' => 'Manajemen', 'ipk' => 3.90],
            ['nim' => '2020105', 'name' => 'Dewi Prasetyo', 'email' => 'dewi.prasetyo@example.com', 'jk' => 'P', 'prodi' => 'Akuntansi', 'ipk' => 3.55],
            ['nim' => '2020106', 'name' => 'Fajar Nugroho', 'email' => 'fajar.nugroho@example.com', 'jk' => 'L', 'prodi' => 'Akuntansi', 'ipk' => 3.70],
            ['nim' => '2020107', 'name' => 'Maya Putri', 'email' => 'maya.putri@example.com', 'jk' => 'P', 'prodi' => 'Hukum', 'ipk' => 3.80],
            ['nim' => '2020108', 'name' => 'Rizki Andika', 'email' => 'rizki.andika@example.com', 'jk' => 'L', 'prodi' => 'Hukum', 'ipk' => 3.50],
            ['nim' => '2020109', 'name' => 'Nur Indah', 'email' => 'nur.indah@example.com', 'jk' => 'P', 'prodi' => 'Pendidikan', 'ipk' => 3.95],
            ['nim' => '2020110', 'name' => 'Dimas Pratama', 'email' => 'dimas.pratama@example.com', 'jk' => 'L', 'prodi' => 'Pendidikan', 'ipk' => 3.40],
        ];

        foreach ($wisudawanData as $data) {
            $user = User::create([
                'nim' => $data['nim'],
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'), // Default password
                'role' => 'wisudawan',
                'status' => 'aktif',
            ]);

            $predikat = $data['ipk'] >= 3.76 ? 'Cumlaude' : ($data['ipk'] >= 3.51 ? 'Sangat Memuaskan' : 'Memuaskan');

            Wisudawan::create([
                'user_id' => $user->id,
                'prodi' => $data['prodi'],
                'fakultas' => 'Fakultas Sains & Teknologi',
                'ipk' => $data['ipk'],
                'predikat' => $predikat,
                'jenis_kelamin' => $data['jk'],
                'telepon' => '08' . rand(1000000000, 9999999999),
                'nama_ortu' => 'Orang Tua ' . $data['name'],
                'jumlah_tamu' => rand(1, 3),
                'hari_wisuda' => rand(1, 2),
            ]);
        }

        // Create kursi (seats) - 4 sections x 100 seats x 2 days
        $sections = [
            'A' => 'P', // Depan Kiri - Perempuan
            'B' => 'L', // Belakang Kiri - Laki-laki
        ];

        foreach ([1, 2] as $hari) {
            foreach ($sections as $section => $gender) {
                for ($i = 1; $i <= 100; $i++) {
                    $kodeKursi = $section . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    Kursi::create([
                        'kode_kursi' => $kodeKursi,
                        'section' => $section,
                        'nomor' => $i,
                        'hari' => $hari,
                        'jenis_kelamin' => $gender,
                        'wisudawan_id' => null,
                    ]);
                }
            }
        }

        // Assign some wisudawan to seats
        $wisudawanList = Wisudawan::with('user')->get();
        foreach ($wisudawanList as $w) {
            $section = $w->jenis_kelamin === 'P' ? ['A'] : ['B'];
            $seat = Kursi::whereIn('section', $section)
                ->where('hari', $w->hari_wisuda)
                ->whereNull('wisudawan_id')
                ->first();
            if ($seat) {
                $seat->update(['wisudawan_id' => $w->id]);
            }
        }

        echo "Database seeded successfully!\n";
        echo "Admin: admin@unsiq.ac.id / admin123\n";
        echo "Wisudawan: 2020101-2020110 / password\n";
    }
}


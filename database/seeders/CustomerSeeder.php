<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Nama-nama umum pelanggan Indonesia
     */
    private array $firstNames = [
        'Ahmad', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hendra',
        'Indah', 'Joko', 'Karina', 'Lina', 'Mira', 'Nanda', 'Oka', 'Putri',
        'Qianna', 'Rina', 'Siti', 'Toni', 'Udin', 'Vita', 'Wahyu', 'Xenia',
        'Yudi', 'Zara', 'Anita', 'Bambang', 'Cahyono', 'Dani', 'Erika',
        'Faisal', 'Galuh', 'Handoko', 'Irma', 'Jaka', 'Kusuma', 'Lusiana',
        'Magdalena', 'Novi', 'Oscar', 'Puspita', 'Rama', 'Sinta', 'Tara',
        'Umar', 'Vika', 'Wulandari', 'Yanuar', 'Zahra'
    ];

    private array $lastNames = [
        'Wijaya', 'Santoso', 'Kurniawan', 'Hidayat', 'Rahman', 'Setiawan',
        'Hartono', 'Winarno', 'Suryanto', 'Permata', 'Kusuma', 'Pratama',
        'Hermawan', 'Gunawan', 'Handayani', 'Suharto', 'Prabowo', 'Yudhoyono',
        'Soeharto', 'Soekarno', 'Haryanto', 'Supriyanto', 'Riyanto', 'Prihatno',
        'Ardianto', 'Budiman', 'Cahyono', 'Darwanto', 'Eka', 'Farros', 'Gunawan',
        'Hadi', 'Ismail', 'Jamaludin', 'Kemal', 'Lukas', 'Mahmud', 'Nurdin',
        'Osman', 'Parman', 'Qanita', 'Ridho', 'Syaiful', 'Taufik', 'Ulfah'
    ];

    private array $provinces = [
        '11', '12', '13', '14', '15', '16', '17', '18', '19', '21',
        '31', '32', '33', '34', '35', '36', '51', '52', '53', '61',
        '62', '63', '64', '65', '71', '72', '73', '74', '75', '76'
    ];

    public function run(): void
    {
        // Create 10 specific pending applicants (customers who registered as sellers)
        $pendingApplicants = [
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@example.com'],
            ['name' => 'Siti Aminah', 'email' => 'siti.aminah@example.com'],
            ['name' => 'Rizky Pratama', 'email' => 'rizky.pratama@example.com'],
            ['name' => 'Dewi Sartika', 'email' => 'dewi.sartika@example.com'],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@example.com'],
            ['name' => 'Ratna Wulandari', 'email' => 'ratna.wulan@example.com'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko.prasetyo@example.com'],
            ['name' => 'Nurul Hidayah', 'email' => 'nurul.hidayah@example.com'],
            ['name' => 'Dimas Anggara', 'email' => 'dimas.anggara@example.com'],
            ['name' => 'Fitri Handayani', 'email' => 'fitri.handayani@example.com'],
        ];

        foreach ($pendingApplicants as $idx => $app) {
            $user = User::firstOrCreate(
                ['email' => $app['email']],
                [
                    'name' => $app['name'],
                    'phone' => '08' . rand(10, 99) . rand(10000000, 99999999),
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                ]
            );

            // create seller application record in sellers table with pending status
            \App\Models\Seller::firstOrCreate(
                ['user_id' => $user->user_id],
                [
                    'store_name' => 'Applicant - ' . $user->name,
                    'store_description' => 'Permohonan pendaftaran seller oleh ' . $user->name,
                    'phone' => $user->phone,
                    'pic_name' => $user->name, // required field (PIC name)
                    'address' => null,
                    'province_id' => null,
                    'city_id' => null,
                    'district_id' => null,
                    'village_id' => null,
                    'ktp_number' => null,
                    'ktp_file_path' => null,
                    'pic_file_path' => null,
                    'status' => 'pending',
                    'verified_at' => null,
                    'is_active' => false,
                ]
            );
        }
    }
}

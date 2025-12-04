<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Nama toko yang relevan untuk 2025 Indonesia
     */
    private array $storeNames = [
        'Toko Elektronik Jaya',
        'Fashion Center Indonesia',
        'Rumah Cantik Online',
        'Beauty Plus Pro',
        'Kuliner Nusantara',
        'Olahraga Maju Jaya',
        'Toko Buku Cerdas',
        'Mainan Ceria Anak',
        'Otomotif Premier',
        'Dapur Modern Indonesia',
        'Elektronik Digital Terpercaya',
        'Fashion Trendy Style',
        'Peralatan Rumah Tangga',
        'Skincare Beauty House',
        'Makanan Premium Berkualitas',
        'Olahraga Sehat Bersama',
        'Toko Edukasi dan Literasi',
        'Mainan Edukatif Pintar',
        'Aksesoris Kendaraan Lengkap',
        'Peralatan Masak Profesional',
    ];

    private array $storeDescriptions = [
        'Toko online terpercaya menjual berbagai produk berkualitas dengan harga kompetitif',
        'Kami menyediakan produk pilihan dengan jaminan keaslian dan garansi resmi',
        'Pengalaman berbelanja terbaik dengan layanan pelanggan yang responsif',
        'Produk original dengan sertifikat keaslian dan garansi resmi',
        'Menjual produk terpilih dengan harga terjangkau dan kualitas terjamin',
        'Belanja mudah, aman, dan terpercaya dengan berbagai pilihan pembayaran',
        'Toko pilihan dengan koleksi lengkap dan update produk setiap hari',
        'Pelayanan terbaik untuk kepuasan berbelanja Anda',
    ];

    private array $picNames = [
        'Budi Hartono', 'Siti Fatimah', 'Rudi Setiawan', 'Intan Kusuma',
        'Ahmad Wijaya', 'Dewi Handayani', 'Joko Susanto', 'Ratna Pertiwi',
        'Hendra Gunawan', 'Sinta Rahayu', 'Bambang Irawan', 'Lina Marlina',
        'Dani Hermawan', 'Wulandari Putri', 'Fajar Hidayat', 'Indah Suryanti',
        'Kardi Supriyanto', 'Mita Cahyani', 'Agus Prabowo', 'Yuni Kartini',
    ];

    private array $provinces = [
        '11', '12', '13', '14', '15', '16', '17', '18', '19', '21',
        '31', '32', '33', '34', '35', '36', '51', '52', '53', '61'
    ];

    private array $provinceCities = [
        '11' => '1101', '12' => '1201', '13' => '1301', '14' => '1401', '15' => '1501',
        '16' => '1601', '17' => '1701', '18' => '1801', '19' => '1901', '21' => '2101',
        '31' => '3101', '32' => '3201', '33' => '3301', '34' => '3401', '35' => '3501',
        '36' => '3601', '51' => '5101', '52' => '5201', '53' => '5301', '61' => '6101'
    ];

    /**
     * Valid location codes (village level) untuk reference
     * Menggunakan kombinasi valid province, city, district, village
     */
    private array $validLocations = [
        ['province' => '11', 'city' => '1101', 'district' => '110101', 'village' => '1101012001'],
        ['province' => '11', 'city' => '1101', 'district' => '110101', 'village' => '1101012002'],
        ['province' => '11', 'city' => '1101', 'district' => '110101', 'village' => '1101012003'],
        ['province' => '12', 'city' => '1201', 'district' => '120101', 'village' => '1201012001'],
        ['province' => '12', 'city' => '1201', 'district' => '120101', 'village' => '1201012002'],
        ['province' => '12', 'city' => '1201', 'district' => '120101', 'village' => '1201012003'],
        ['province' => '13', 'city' => '1301', 'district' => '130101', 'village' => '1301012001'],
        ['province' => '13', 'city' => '1301', 'district' => '130101', 'village' => '1301012002'],
        ['province' => '13', 'city' => '1301', 'district' => '130101', 'village' => '1301012003'],
        ['province' => '14', 'city' => '1401', 'district' => '140101', 'village' => '1401012001'],
        ['province' => '15', 'city' => '1501', 'district' => '150101', 'village' => '1501012001'],
        ['province' => '16', 'city' => '1601', 'district' => '160101', 'village' => '1601012001'],
        ['province' => '17', 'city' => '1701', 'district' => '170101', 'village' => '1701012001'],
        ['province' => '18', 'city' => '1801', 'district' => '180101', 'village' => '1801012001'],
        ['province' => '19', 'city' => '1901', 'district' => '190101', 'village' => '1901012001'],
        ['province' => '21', 'city' => '2101', 'district' => '210101', 'village' => '2101012001'],
        ['province' => '31', 'city' => '3101', 'district' => '310101', 'village' => '3101012001'],
        ['province' => '32', 'city' => '3201', 'district' => '320101', 'village' => '3201012001'],
        ['province' => '33', 'city' => '3301', 'district' => '330101', 'village' => '3301012001'],
        ['province' => '34', 'city' => '3401', 'district' => '340101', 'village' => '3401012001'],
    ];

    public function run(): void
    {
        // Create 3 specific active sellers as requested
        $activeSellers = [
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.batik@example.com',
                'password' => bcrypt('password'),
                'role' => 'seller',
                'store_name' => 'Batik Pesona Jawa',
                'status' => 'approved',
                'is_active' => true,
            ],
            [
                'name' => 'Sri Wahyuni',
                'email' => 'sri.dapur@example.com',
                'password' => bcrypt('password'),
                'role' => 'seller',
                'store_name' => 'Dapur Bu Sri',
                'status' => 'approved',
                'is_active' => true,
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.tekno@example.com',
                'password' => bcrypt('password'),
                'role' => 'seller',
                'store_name' => 'Glodok Elektronik Semarang',
                'status' => 'approved',
                'is_active' => true,
            ],
        ];

        foreach ($activeSellers as $sdata) {
            $user = User::firstOrCreate(
                ['email' => $sdata['email']],
                [
                    'name' => $sdata['name'],
                    'phone' => '08' . rand(10, 99) . rand(10000000, 99999999),
                    'password' => $sdata['password'],
                    'role' => 'seller',
                ]
            );

            Seller::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'store_name' => $sdata['store_name'],
                    'store_description' => $this->storeDescriptions[array_rand($this->storeDescriptions)],
                    'phone' => $user->phone,
                    'pic_name' => $this->picNames[array_rand($this->picNames)],
                    'address' => 'Jl. ' . implode(' ', array_slice(explode(' ', fake()->address()), 0, 3)),
                    'rt' => str_pad(rand(1, 20), 3, '0', STR_PAD_LEFT),
                    'rw' => str_pad(rand(1, 15), 3, '0', STR_PAD_LEFT),
                    'province_id' => $this->validLocations[array_rand($this->validLocations)]['province'],
                    'city_id' => $this->validLocations[array_rand($this->validLocations)]['city'],
                    'district_id' => $this->validLocations[array_rand($this->validLocations)]['district'],
                    'village_id' => $this->validLocations[array_rand($this->validLocations)]['village'],
                    'ktp_number' => $this->generateKTPNumber(),
                    'status' => $sdata['status'],
                    'verified_at' => now(),
                    'is_active' => $sdata['is_active'],
                ]
            );
        }

        // continue creating additional generic sellers as before
        for ($i = 0; $i < 20; $i++) {
            // Create user with seller role
            $user = User::create([
                'name' => $this->generateSellerName(),
                'email' => 'seller' . ($i + 1) . '@example.com',
                'phone' => '08' . rand(10, 99) . rand(10000000, 99999999),
                'password' => bcrypt('password'),
                'role' => 'seller',
            ]);

            // Get valid location with proper hierarchy
            $location = $this->validLocations[$i % count($this->validLocations)];

            Seller::create([
                'user_id' => $user->user_id,
                'store_name' => $this->storeNames[array_rand($this->storeNames)] . ' #' . ($i + 1),
                'store_description' => $this->storeDescriptions[array_rand($this->storeDescriptions)],
                'phone' => $user->phone,
                'pic_name' => $this->picNames[array_rand($this->picNames)],
                'address' => 'Jl. ' . implode(' ', array_slice(explode(' ', fake()->address()), 0, 3)),
                'rt' => str_pad(rand(1, 20), 3, '0', STR_PAD_LEFT),
                'rw' => str_pad(rand(1, 15), 3, '0', STR_PAD_LEFT),
                'province_id' => $location['province'],
                'city_id' => $location['city'],
                'district_id' => $location['district'],
                'village_id' => $location['village'],
                'ktp_number' => $this->generateKTPNumber(),
                'ktp_file_path' => null,
                'pic_file_path' => null,
                'status' => $i < 15 ? 'approved' : 'pending', // 15 approved, 5 pending
                'verified_at' => $i < 15 ? now() : null,
                'is_active' => $i < 18 ? true : false, // 18 aktif, 2 tidak aktif
            ]);
        }
    }

    private function generateSellerName(): string
    {
        $firstNames = [
            'Budi', 'Siti', 'Rudi', 'Intan', 'Ahmad', 'Dewi', 'Joko', 'Ratna',
            'Hendra', 'Sinta', 'Bambang', 'Lina', 'Dani', 'Wulandari', 'Fajar'
        ];
        $lastNames = [
            'Hartono', 'Fatimah', 'Setiawan', 'Kusuma', 'Wijaya', 'Handayani',
            'Susanto', 'Pertiwi', 'Gunawan', 'Rahayu', 'Irawan', 'Marlina'
        ];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function generateKTPNumber(): string
    {
        return rand(1000000000000000, 9999999999999999);
    }
}

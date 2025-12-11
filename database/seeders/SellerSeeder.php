<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SellerSeeder extends Seeder
{
    /**
     * Valid location codes (village level) untuk Jawa Tengah (Province 33)
     * Menggunakan kombinasi valid province, city, district, village
     */
    private array $javaTengahLocations = [
        ['province' => '33', 'city' => '3301', 'district' => '330101', 'village' => '3301012001'],
        ['province' => '33', 'city' => '3301', 'district' => '330101', 'village' => '3301012002'],
        ['province' => '33', 'city' => '3301', 'district' => '330101', 'village' => '3301012003'],
        ['province' => '33', 'city' => '3302', 'district' => '330201', 'village' => '3302012001'],
        ['province' => '33', 'city' => '3302', 'district' => '330201', 'village' => '3302012002'],
        ['province' => '33', 'city' => '3303', 'district' => '330301', 'village' => '3303012001'],
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

    public function run(): void
    {
        // Copy sample files from frontend assets to backend storage
        $this->copyFilesFromFrontend();

        // Create 3 specific sellers as requested
        $sellers = [
            [
                'name' => 'Mohamad Solkhan Nawawi',
                'email' => 'mohamad.solkhan@example.com',
                'phone' => '08237328582',
                'pic_name' => 'Mohamad Solkhan Nawawi',
                'store_name' => 'Toko Mohamad Solkhan',
            ],
            [
                'name' => 'Muhamad Sahal Annabil',
                'email' => 'muhamad.sahal@example.com',
                'phone' => '089647652134',
                'pic_name' => 'Muhamad Sahal Annabil',
                'store_name' => 'Toko Muhamad Sahal',
            ],
            [
                'name' => 'Ivan Pratomo Soelistio',
                'email' => 'ivan.pratomo@example.com',
                'phone' => '08988931042',
                'pic_name' => 'Ivan Pratomo Soelistio',
                'store_name' => 'Toko Ivan Pratomo',
            ],
        ];

        foreach ($sellers as $idx => $sellerData) {
            // Create user
            $user = User::create([
                'name' => $sellerData['name'],
                'email' => $sellerData['email'],
                'phone' => $sellerData['phone'],
                'password' => bcrypt('Password123.'),
                'role' => 'seller',
            ]);

            // Get location from Jawa Tengah
            $location = $this->javaTengahLocations[$idx % count($this->javaTengahLocations)];

            // Create seller
            Seller::create([
                'user_id' => $user->user_id,
                'store_name' => $sellerData['store_name'],
                'store_description' => $this->storeDescriptions[$idx],
                'phone' => $sellerData['phone'],
                'pic_name' => $sellerData['pic_name'],
                'address' => 'Jl. Diponegoro No. ' . (100 + $idx),
                'rt' => '001',
                'rw' => '001',
                'province_id' => $location['province'],
                'city_id' => $location['city'],
                'district_id' => $location['district'],
                'village_id' => $location['village'],
                'ktp_number' => $this->generateKTPNumber(),
                'ktp_file_path' => 'documents/ktp/samplektp.png',
                'pic_file_path' => 'images/pic/samplepic.png',
                'status' => 'approved',
                'verified_at' => now(),
                'is_active' => true,
            ]);
        }
    }

    private function generateKTPNumber(): string
    {
        return rand(1000000000000000, 9999999999999999);
    }

    private function copyFilesFromFrontend(): void
    {
        // Paths
        $frontendKtpSource = base_path('../frontend/src/assets/images/ktp/samplektp.png');
        $frontendPicSource = base_path('../frontend/src/assets/images/pic/samplepic.png');
        
        $ktpDest = 'documents/ktp/samplektp.png';
        $picDest = 'images/pic/samplepic.png';

        // Ensure directories exist
        $ktpDir = storage_path('app/private/documents/ktp');
        $picDir = storage_path('app/public/images/pic');
        
        if (!File::isDirectory($ktpDir)) {
            File::makeDirectory($ktpDir, 0755, true);
        }
        if (!File::isDirectory($picDir)) {
            File::makeDirectory($picDir, 0755, true);
        }

        // Copy KTP file to private storage
        if (File::exists($frontendKtpSource)) {
            Storage::disk('local')->put($ktpDest, File::get($frontendKtpSource));
        }

        // Copy PIC file to public storage
        if (File::exists($frontendPicSource)) {
            Storage::disk('public')->put($picDest, File::get($frontendPicSource));
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CustomerSeeder extends Seeder
{
    /**
     * 10 customer dengan lokasi berbeda (Banten 36, Bali 51, Jawa Tengah 33)
     */
    private array $customers = [
        // Banten (36)
        [
            'name' => 'Ahmad Hidayat',
            'email' => 'ahmad.hidayat@example.com',
            'phone' => '081234567890',
            'province_id' => '36',
            'city_id' => '3601',
            'district_id' => '360101',
            'village_id' => '3601012001',
        ],
        [
            'name' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@example.com',
            'phone' => '081234567891',
            'province_id' => '36',
            'city_id' => '3601',
            'district_id' => '360101',
            'village_id' => '3601012002',
        ],
        [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'phone' => '081234567892',
            'province_id' => '36',
            'city_id' => '3602',
            'district_id' => '360201',
            'village_id' => '3602012001',
        ],
        // Bali (51)
        [
            'name' => 'Made Wijaya',
            'email' => 'made.wijaya@example.com',
            'phone' => '081234567893',
            'province_id' => '51',
            'city_id' => '5101',
            'district_id' => '510101',
            'village_id' => '5101012001',
        ],
        [
            'name' => 'Ketut Santoso',
            'email' => 'ketut.santoso@example.com',
            'phone' => '081234567894',
            'province_id' => '51',
            'city_id' => '5101',
            'district_id' => '510101',
            'village_id' => '5101012002',
        ],
        [
            'name' => 'Ni Wayan Kusuma',
            'email' => 'ni.wayan.kusuma@example.com',
            'phone' => '081234567895',
            'province_id' => '51',
            'city_id' => '5102',
            'district_id' => '510201',
            'village_id' => '5102012001',
        ],
        // Jawa Tengah (33)
        [
            'name' => 'Rini Pramesti',
            'email' => 'rini.pramesti@example.com',
            'phone' => '081234567896',
            'province_id' => '33',
            'city_id' => '3301',
            'district_id' => '330101',
            'village_id' => '3301012001',
        ],
        [
            'name' => 'Hendra Gunawan',
            'email' => 'hendra.gunawan@example.com',
            'phone' => '081234567897',
            'province_id' => '33',
            'city_id' => '3302',
            'district_id' => '330201',
            'village_id' => '3302012001',
        ],
        [
            'name' => 'Dwi Lestari',
            'email' => 'dwi.lestari@example.com',
            'phone' => '081234567898',
            'province_id' => '33',
            'city_id' => '3303',
            'district_id' => '330301',
            'village_id' => '3303012001',
        ],
        [
            'name' => 'Yanuartomo',
            'email' => 'yanuartomo@example.com',
            'phone' => '081234567899',
            'province_id' => '33',
            'city_id' => '3304',
            'district_id' => '330401',
            'village_id' => '3304012001',
        ],
    ];

    public function run(): void
    {
        // Copy sample files from frontend assets to backend storage
        $this->copyFilesFromFrontend();

        foreach ($this->customers as $customerData) {
            // Create user with customer role
            $user = User::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'password' => bcrypt('Password123.'),
                'role' => 'customer',
            ]);

            // Create seller application with pending status
            Seller::create([
                'user_id' => $user->user_id,
                'store_name' => 'Applicant - ' . $user->name,
                'store_description' => 'Permohonan pendaftaran seller oleh ' . $user->name,
                'phone' => $user->phone,
                'pic_name' => $user->name,
                'address' => 'Jl. ' . ucfirst(str_repeat('Jalan', 1)) . ' ' . $customerData['name'],
                'rt' => '001',
                'rw' => '001',
                'province_id' => $customerData['province_id'],
                'city_id' => $customerData['city_id'],
                'district_id' => $customerData['district_id'],
                'village_id' => $customerData['village_id'],
                'ktp_number' => $this->generateKTPNumber(),
                'ktp_file_path' => 'documents/ktp/samplektp.png',
                'pic_file_path' => 'images/pic/samplepic.png',
                'status' => 'pending',
                'verified_at' => null,
                'is_active' => false,
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

        // Copy KTP file to private storage (only if not already copied)
        if (File::exists($frontendKtpSource) && !Storage::disk('local')->exists($ktpDest)) {
            Storage::disk('local')->put($ktpDest, File::get($frontendKtpSource));
        }

        // Copy PIC file to public storage (only if not already copied)
        if (File::exists($frontendPicSource) && !Storage::disk('public')->exists($picDest)) {
            Storage::disk('public')->put($picDest, File::get($frontendPicSource));
        }
    }
}

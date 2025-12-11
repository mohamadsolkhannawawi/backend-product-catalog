<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seller>
 */
class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        
        // Generate dummy KTP file path
        $ktpFileName = 'ktp_' . uniqid() . '.txt';
        $ktpPath = 'private/ktp/' . $ktpFileName;
        
        // Generate dummy PIC file path
        $picFileName = 'pic_' . uniqid() . '.txt';
        $picPath = 'seller-pic/' . $picFileName;
        
        // Create dummy files (text placeholders for now)
        try {
            Storage::disk('local')->makeDirectory('private/ktp', 0755, true);
            Storage::disk('public')->makeDirectory('seller-pic', 0755, true);
            Storage::disk('local')->put($ktpPath, 'KTP Placeholder Document');
            Storage::disk('public')->put($picPath, 'PIC Photo Placeholder');
        } catch (\Exception $e) {
            // Silently skip file creation if it fails
        }
        
        // Array of Indonesian provinces (code, city_code, district_code, village_code)
        // Format: [province_id, city_id, district_id, village_id]
        $provinces = [
            ['12', '1201', '120101', '12010101'], // DKI Jakarta
            ['11', '1101', '110101', '11010101'], // Jawa Barat
            ['33', '3301', '330101', '33010101'], // Jawa Timur
            ['34', '3401', '340101', '34010101'], // Jawa Tengah
            ['51', '5101', '510101', '51010101'], // Bali
            ['13', '1301', '130101', '13010101'], // Banten
            ['15', '1501', '150101', '15010101'], // Sumatera Selatan
            ['14', '1401', '140101', '14010101'], // Yogyakarta
        ];
        
        $selectedProvince = $this->faker->randomElement($provinces);

        return [
            'user_id' => $user->user_id,
            'store_name' => $this->faker->company(),
            'store_description' => $this->faker->sentence(),
            'phone' => '0812' . $this->faker->numerify('########'),
            'pic_name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'rt' => str_pad($this->faker->numberBetween(1, 50), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad($this->faker->numberBetween(1, 30), 3, '0', STR_PAD_LEFT),
            'province_id' => $selectedProvince[0],
            'city_id' => $selectedProvince[1],
            'district_id' => $selectedProvince[2],
            'village_id' => $selectedProvince[3],
            'ktp_number' => $this->faker->numerify('################'),
            'ktp_file_path' => $ktpPath,
            'pic_file_path' => $picPath,
            'status' => 'approved',
            'is_active' => true,
            'verified_at' => now(),
        ];
    }
}

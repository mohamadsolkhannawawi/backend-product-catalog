<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seller>
 */
class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->user_id,
            'store_name' => $this->faker->company(),
            'store_description' => $this->faker->sentence(),
            'phone' => '0812' . $this->faker->numerify('########'),
            'address' => $this->faker->address(),
            'rt' => '001',
            'rw' => '002',
            'province_id' => '12',
            'city_id' => '1201',
            'district_id' => '120101',
            'village_id' => '12010101',
            'ktp_number' => $this->faker->numerify('################'),
            'ktp_file_path' => null,
            'pic_file_path' => null,
            'status' => 'approved',
            'is_active' => true,
        ];
    }
}

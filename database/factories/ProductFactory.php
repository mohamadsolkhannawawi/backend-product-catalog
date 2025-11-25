<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $seller = Seller::factory()->create();

        return [
            'seller_id' => $seller->seller_id,
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'category' => 'general',
            'price' => $this->faker->randomFloat(2, 1000, 100000),
            'stock' => $this->faker->numberBetween(1, 100),
            'images' => [],
            'is_active' => true,
        ];
    }
}

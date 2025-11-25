<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $product = Product::factory()->create();

        return [
            'product_id' => $product->product_id,
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'province_id' => '12',
            'phone' => null,
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(),
        ];
    }
}

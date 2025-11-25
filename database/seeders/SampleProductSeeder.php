<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Seller;

class SampleProductSeeder extends Seeder
{
    public function run()
    {
        $seller = Seller::first();

        if (! $seller) {
            // If no seller exists, create one via SampleSellerSeeder
            $this->call(SampleSellerSeeder::class);
            $seller = Seller::first();
        }

        Product::create([
            'seller_id' => $seller->seller_id,
            'name' => 'Produk Contoh 1',
            'slug' => 'produk-contoh-1-' . uniqid(),
            'price' => 15000,
            'stock' => 10,
            'category' => 'Contoh',
            'images' => json_encode(['/storage/products/sample1.png']),
            'description' => 'Produk contoh pertama.',
            'visitor' => 12, // VALID
        ]);

        Product::create([
            'seller_id' => $seller->seller_id,
            'name' => 'Produk Contoh 2',
            'slug' => 'produk-contoh-2-' . uniqid(),
            'price' => 25000,
            'stock' => 5,
            'category' => 'Contoh',
            'images' => json_encode(['/storage/products/sample2.png']),
            'description' => 'Produk contoh kedua.',
            'visitor' => 5, // VALID
        ]);
    }
}

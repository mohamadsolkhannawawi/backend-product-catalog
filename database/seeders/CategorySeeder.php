<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only top-level categories: no subcategories
        $categories = [
            ['name' => 'Fashion & Aksesoris', 'slug' => 'fashion-aksesoris', 'icon' => 'Shirt'],
            ['name' => 'Batik & Wastra Nusantara', 'slug' => 'batik-wastra', 'icon' => 'Scissors'],
            ['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman', 'icon' => 'Utensils'],
            ['name' => 'Kerajinan & Seni (Kriya)', 'slug' => 'kerajinan-seni', 'icon' => 'Brush'],
            ['name' => 'Elektronik & Gadget', 'slug' => 'elektronik-gadget', 'icon' => 'Smartphone'],
            ['name' => 'Komputer & Laptop', 'slug' => 'komputer-laptop', 'icon' => 'Monitor'],
            ['name' => 'Perlengkapan Rumah & Dekorasi', 'slug' => 'rumah-dekorasi', 'icon' => 'Home'],
            ['name' => 'Kecantikan & Perawatan Diri', 'slug' => 'kecantikan-perawatan', 'icon' => 'Sparkles'],
            ['name' => 'Kesehatan & Medis', 'slug' => 'kesehatan-medis', 'icon' => 'Activity'],
            ['name' => 'Ibu, Bayi & Anak', 'slug' => 'ibu-bayi-anak', 'icon' => 'Baby'],
            ['name' => 'Hobi & Olahraga', 'slug' => 'hobi-olahraga', 'icon' => 'Trophy'],
            ['name' => 'Otomotif & Aksesoris', 'slug' => 'otomotif', 'icon' => 'Car'],
            ['name' => 'Buku & Alat Tulis', 'slug' => 'buku-alat-tulis', 'icon' => 'BookOpen'],
        ];

        foreach ($categories as $data) {
            $parent = Category::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['name'],
                    'icon' => $data['icon'] ?? null,
                ]
            );

            if (!empty($data['sub'])) {
                foreach ($data['sub'] as $subName) {
                    $subSlug = Str::slug($subName);
                    Category::updateOrCreate(
                        ['slug' => $subSlug, 'parent_id' => $parent->category_id],
                        [
                            'name' => $subName,
                            'description' => $subName,
                            'icon' => null,
                            'parent_id' => $parent->category_id,
                        ]
                    );
                }
            }
        }
    }
}


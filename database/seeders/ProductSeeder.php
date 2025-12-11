<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Seller;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * 12 produk untuk 3 seller (4 produk per seller)
     * Diambil dari nama file gambar yang sudah disediakan
     */
    private array $productsData = [
        // Seller 1 - Mohamad Solkhan Nawawi (4 produk)
        [
            'name' => 'Tas Bahu (Shoulder Bag)',
            'description' => 'Tas bahu ergonomis dengan desain modern dan bahan berkualitas tinggi',
            'price' => 450000,
            'stock' => 15,
            'category_slug' => 'fashion-aksesoris',
            'seller_index' => 0,
            'image' => 'product/1. Tas Bahu (Shoulder Bag) 1.png',
        ],
        [
            'name' => 'Speaker Bluetooth Portabel',
            'description' => 'Speaker wireless portabel dengan kualitas suara jernih dan baterai tahan lama',
            'price' => 350000,
            'stock' => 20,
            'category_slug' => 'elektronik-gadget',
            'seller_index' => 0,
            'image' => 'product/2. Speaker Bluetooth Portabel.jpg',
        ],
        [
            'name' => 'Smartphone Mid-range 128GB',
            'description' => 'Smartphone dengan prosesor cepat, RAM besar, dan kamera berkualitas tinggi',
            'price' => 3500000,
            'stock' => 8,
            'category_slug' => 'elektronik-gadget',
            'seller_index' => 0,
            'image' => 'product/3. Smartphone Mid-range 128GB.jpeg',
        ],
        [
            'name' => 'Set Cat Akrilik 12 Warna',
            'description' => 'Set lengkap cat akrilik berkualitas untuk seni dan kerajinan',
            'price' => 120000,
            'stock' => 30,
            'category_slug' => 'kerajinan-seni',
            'seller_index' => 0,
            'image' => 'product/4. Set Cat Akrilik 12 Warna 2.jpg',
        ],
        
        // Seller 2 - Muhamad Sahal Annabil (4 produk)
        [
            'name' => 'Rak Buku Minimalis',
            'description' => 'Rak buku desain minimalis yang cocok untuk dekorasi rumah modern',
            'price' => 280000,
            'stock' => 12,
            'category_slug' => 'rumah-dekorasi',
            'seller_index' => 1,
            'image' => 'product/5. Rak Buku Minimalis.jpg',
        ],
        [
            'name' => 'Pengharum Mobil Aroma Kopi',
            'description' => 'Pengharum mobil dengan aroma kopi yang menyegarkan dan tahan lama',
            'price' => 85000,
            'stock' => 50,
            'category_slug' => 'otomotif',
            'seller_index' => 1,
            'image' => 'product/6. Pengharum Mobil Aroma Kopi 3.jpg',
        ],
        [
            'name' => 'Mobil Remote Control Off-Road',
            'description' => 'Mobil RC off-road dengan desain tangguh dan daya cengkeram grip baik',
            'price' => 580000,
            'stock' => 10,
            'category_slug' => 'hobi-olahraga',
            'seller_index' => 1,
            'image' => 'product/7. Mobil Remote Control Off-Road.jpg',
        ],
        [
            'name' => 'Meja Kerja',
            'description' => 'Meja kerja ergonomis dengan desain minimalis cocok untuk home office',
            'price' => 1200000,
            'stock' => 6,
            'category_slug' => 'rumah-dekorasi',
            'seller_index' => 1,
            'image' => 'product/8. Merja Kerja PNG.png',
        ],
        
        // Seller 3 - Ivan Pratomo Soelistio (4 produk)
        [
            'name' => 'Matras Yoga Anti-Slip',
            'description' => 'Matras yoga berkualitas dengan permukaan anti-slip dan nyaman digunakan',
            'price' => 250000,
            'stock' => 25,
            'category_slug' => 'hobi-olahraga',
            'seller_index' => 2,
            'image' => 'product/9. Matras Yoga Anti-Slip.jpg',
        ],
        [
            'name' => 'Keripik Tempe Sagu 250g',
            'description' => 'Keripik tempe sagu renyah dengan kemasan higienis 250 gram',
            'price' => 45000,
            'stock' => 100,
            'category_slug' => 'makanan-minuman',
            'seller_index' => 2,
            'image' => 'product/10. Keripik Tempe Sagu 250g.png',
        ],
        [
            'name' => 'Headphone Noise Cancelling',
            'description' => 'Headphone dengan teknologi noise cancelling untuk pengalaman audio terbaik',
            'price' => 1500000,
            'stock' => 9,
            'category_slug' => 'elektronik-gadget',
            'seller_index' => 2,
            'image' => 'product/11. Headphone Noise Cancelling.jpg',
        ],
        [
            'name' => 'Buku Filosofi Teras',
            'description' => 'Buku self-help favorit tentang filosofi Yunani dan praktik hidup sehat',
            'price' => 85000,
            'stock' => 35,
            'category_slug' => 'buku-alat-tulis',
            'seller_index' => 2,
            'image' => 'product/12. Buku Filosofi Teras.png',
        ],
    ];

    private array $categoryMappings = [
        'fashion-aksesoris' => ['name' => 'Fashion & Aksesoris', 'description' => 'Produk fashion dan pakaian'],
        'elektronik-gadget' => ['name' => 'Elektronik & Gadget', 'description' => 'Produk elektronik dan gadget'],
        'kerajinan-seni' => ['name' => 'Kerajinan & Seni (Kriya)', 'description' => 'Produk seni dan kerajinan'],
        'rumah-dekorasi' => ['name' => 'Perlengkapan Rumah & Dekorasi', 'description' => 'Perlengkapan rumah tangga dan dekorasi'],
        'otomotif' => ['name' => 'Otomotif & Aksesoris', 'description' => 'Aksesori dan perlengkapan otomotif'],
        'hobi-olahraga' => ['name' => 'Hobi & Olahraga', 'description' => 'Produk mainan dan hobi'],
        'makanan-minuman' => ['name' => 'Makanan & Minuman', 'description' => 'Produk makanan dan minuman'],
        'buku-alat-tulis' => ['name' => 'Buku & Alat Tulis', 'description' => 'Buku dan alat tulis'],
    ];

    public function run(): void
    {
        // Get sellers - should be 3 sellers
        $sellers = Seller::where('status', 'approved')->limit(3)->get();

        if ($sellers->count() < 3) {
            $this->command->warn('Pastikan SellerSeeder sudah membuat 3 seller dengan status approved!');
            return;
        }

        // Create categories if not exist
        foreach ($this->categoryMappings as $slug => $catData) {
            Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                ]
            );
        }

        // Get categories
        $categories = Category::all()->keyBy('slug');

        foreach ($this->productsData as $productData) {
            if (!isset($sellers[$productData['seller_index']])) {
                continue;
            }

            $seller = $sellers[$productData['seller_index']];
            $category = $categories->get($productData['category_slug']);

            if (!$category) {
                continue;
            }

            Product::create([
                'seller_id' => $seller->seller_id,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'category_id' => $category->category_id,
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'primary_image' => $productData['image'],
                'images' => [$productData['image']],
                'visitor' => 0,
                'is_active' => true,
            ]);
        }
    }
}

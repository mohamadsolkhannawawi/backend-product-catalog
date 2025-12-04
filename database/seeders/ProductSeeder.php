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
     * Nama produk realistis Indonesia 2025
     */
    private array $productNames = [
        // Elektronik & Gadget
        'Smartphone Samsung Galaxy A55',
        'Laptop ASUS VivoBook 15',
        'Earbuds Wireless Beats Solo',
        'Power Bank 30000mAh Fast Charging',
        'Smartwatch Xiaomi Band 8',
        'Kamera Mirrorless Canon EOS R50',
        'Tablet iPad 2024',
        'Monitor Gaming 144Hz Curved',

        // Fashion
        'Kemeja Batik Modern Pria',
        'Celana Jeans Premium Panjang',
        'Sepatu Sneaker Casual',
        'Jaket Kulit Asli',
        'Dress Casual Wanita Modern',
        'Kaos Polos Katun Premium',
        'Hoodie Tebal Musim Dingin',
        'Sandal Jepit Kenyamanan Maksimal',

        // Rumah & Taman
        'Lampu LED Smart RGB',
        'Karpet Lantai Anti Slip',
        'Lemari Gantung Dinding',
        'Pot Tanaman Keramik Artistik',
        'Gorden Blackout Tahan Panas',
        'Rak Dinding Minimalis Modern',
        'Meja Makan Kayu Jati',
        'Kursi Gaming Ergonomis',

        // Kecantikan & Perawatan
        'Facial Wash Pagi Malam',
        'Serum Vitamin C Whitening',
        'Masker Wajah Sheet Mask',
        'Sunscreen SPF 50+ PA+++',
        'Toner Hydrating dan Menyegarkan',
        'Body Lotion Pelembab Kulit',
        'Shampoo Anti Ketombe Herbal',
        'Lipstik Tahan Lama 24 Jam',

        // Makanan & Minuman
        'Kopi Robusta Premium Indonesia',
        'Teh Hijau Organik Murni',
        'Snack Keripik Singkong Rasa',
        'Cokelat Hitam Organik',
        'Minyak Zaitun Extra Virgin',
        'Susu Kental Manis Maxi',
        'Kacang Almond Panggang Garlic',
        'Tepung Terigu Cakra Kembar',

        // Olahraga & Outdoor
        'Sepatu Lari Nike Running',
        'Yoga Mat Non-Slip Tebal',
        'Dumbbell Set Stainless Steel',
        'Tenda Camping Waterproof',
        'Tas Hiking 60L Outdoor',
        'Botol Minum Stainless Steel',
        'Raket Badminton Professional',
        'Bola Volley Official Size',

        // Buku & Alat Tulis
        'Buku Fiksi Fantasi Terlaris',
        'Novel Misteri Indonesia',
        'Buku Resep Masakan Rumahan',
        'Pena Gel Premium Hitam',
        'Buku Tulis Sekolah 80 Halaman',
        'Penggaris Panjang 30cm Plastik',
        'Stiker dan Stempel Dekoratif',
        'Buku Catatan Jurnal Leather',

        // Mainan & Hobi
        'Action Figure Superhero Lengkap',
        'Lego Sets Konstruksi Anak',
        'Board Game Keluarga Seru',
        'Puzzle 1000 Pieces Pemandangan',
        'Boneka Karakter Lucu Imut',
        'Mobil Remote Control Offroad',
        'Drone Mini Kamera 4K',
        'Trading Card Game Original',

        // Otomotif
        'Oli Motor Synthetic Premium',
        'Kampas Rem Motor Kualitas OEM',
        'Accu Mobil 12V 100Ah',
        'Lampu LED Mobil Terang',
        'Karpet Mobil Anti Slip',
        'Pembersih Interior Mobil',
        'Pelindung Jok Mobil Premium',
        'Pengharum Mobil Aroma Bunga',

        // Peralatan Dapur
        'Panci Set Teflond Berkualitas',
        'Spatula Silikon Heat Resistant',
        'Pisau Chef Stainless Steel',
        'Talenan Kayu Cutting Board',
        'Mixer Elektrik Powerful',
        'Rice Cooker Stainless Steel',
        'Blender Plastik Tahan Lama',
        'Microwave Dapur Compact',
    ];

    private array $descriptions = [
        'Produk berkualitas tinggi dengan desain modern dan fitur lengkap',
        'Terbuat dari bahan premium, tahan lama, dan ramah lingkungan',
        'Telah teruji dan terpercaya oleh jutaan pengguna di Indonesia',
        'Garansi resmi dari distributor, layanan purna jual terbaik',
        'Harga terjangkau untuk kualitas yang sangat memuaskan',
        'Cocok untuk penggunaan sehari-hari maupun kebutuhan khusus',
        'Desain ergonomis untuk kenyamanan maksimal pengguna',
        'Warna menarik dan pilihan ukuran yang lengkap tersedia',
    ];

    /**
     * Valid sample locations (province, city, district, village)
     * Format: province_code => [city_code, district_code, [village_codes...]]
     */
    private array $validLocations = [
        '11' => ['1101', '110101', ['1101012001', '1101012002', '1101012003']],
        '12' => ['1201', '120101', ['1201012001', '1201012002', '1201012003']],
        '13' => ['1301', '130101', ['1301012001', '1301012002', '1301012003']],
    ];

    private array $sampleImages = [
        'products/sample1.png',
        'products/sample2.png',
        'products/sample3.png',
    ];

    public function run(): void
    {
        // Load approved/active sellers
        $sellers = Seller::where('status', 'approved')->orWhere('is_active', true)->get();

        if ($sellers->isEmpty()) {
            $this->command->warn('Pastikan SellerSeeder sudah dijalankan dan ada seller aktif/approved!');
            return;
        }

        // Definitive product list (30 items) provided by user
        $products = [
            ['name' => 'Kaos Polos Cotton Combed 30s Oversize Lilac', 'category' => 'Fashion & Aksesoris', 'price' => 75000, 'stock' => 150, 'description' => 'Kaos polos gaya Korea bahan adem, cocok untuk outfit harian GenZ.'],
            ['name' => 'Totebag Kanvas Motif Abstrak Handpainted', 'category' => 'Fashion & Aksesoris', 'price' => 45000, 'stock' => 50, 'description' => 'Tas bahu bahan kanvas tebal dengan lukisan tangan unik.'],
            ['name' => 'Jaket Denim Sandwash Vintage', 'category' => 'Fashion & Aksesoris', 'price' => 185000, 'stock' => 30, 'description' => 'Jaket jeans model vintage yang trendy dan tahan lama.'],

            ['name' => 'Kemeja Batik Pria Lengan Panjang Motif Mega Mendung', 'category' => 'Batik & Wastra Nusantara', 'price' => 125000, 'stock' => 60, 'description' => 'Batik printing motif Cirebon, bahan katun prima halus.'],
            ['name' => 'Kain Tenun Ikat Troso Jepara Premium', 'category' => 'Batik & Wastra Nusantara', 'price' => 250000, 'stock' => 20, 'description' => 'Kain tenun asli ATBM (Alat Tenun Bukan Mesin) ukuran 240x120cm.'],

            ['name' => 'Baso Aci Instan Garut Paket Komplit', 'category' => 'Makanan & Minuman', 'price' => 15000, 'stock' => 500, 'description' => 'Jajanan viral isi baso aci, cuanki lidah, dan pilus cikur pedas.'],
            ['name' => 'Kopi Arabika Gayo Aceh 250g - Biji Sangrai', 'category' => 'Makanan & Minuman', 'price' => 85000, 'stock' => 100, 'description' => 'Biji kopi pilihan dari dataran tinggi Gayo, aroma kuat dan nikmat.'],
            ['name' => 'Keripik Pisang Coklat Lumer Lampung', 'category' => 'Makanan & Minuman', 'price' => 25000, 'stock' => 200, 'description' => 'Oleh-oleh khas Lampung, keripik renyah dengan balutan coklat tebal.'],

            ['name' => 'Tas Anyaman Rotan Bulat Bali', 'category' => 'Kerajinan & Seni (Kriya)', 'price' => 120000, 'stock' => 40, 'description' => 'Tas selempang etnik buatan pengrajin Bali, cocok untuk OOTD.'],
            ['name' => 'Hiasan Dinding Ukiran Kayu Jati Jepara', 'category' => 'Kerajinan & Seni (Kriya)', 'price' => 350000, 'stock' => 10, 'description' => 'Ukiran relief detail bahan kayu jati tua asli.'],

            ['name' => 'Speaker Bluetooth Portable Bass Boost Waterproof', 'category' => 'Elektronik & Gadget', 'price' => 199000, 'stock' => 80, 'description' => 'Speaker mini suara mantap, tahan percikan air, baterai awet 8 jam.'],
            ['name' => 'Tripod HP & Kamera Flexible Gorilla Pod', 'category' => 'Elektronik & Gadget', 'price' => 35000, 'stock' => 120, 'description' => 'Tripod gurita fleksibel bisa ditaruh di mana saja untuk ngonten.'],

            ['name' => 'Mouse Wireless Silent Click Rechargeable', 'category' => 'Komputer & Laptop', 'price' => 79000, 'stock' => 90, 'description' => 'Mouse tanpa kabel, klik tidak berisik, baterai bisa diisi ulang.'],
            ['name' => 'Cooling Pad Laptop 15.6 Inch RGB Fan', 'category' => 'Komputer & Laptop', 'price' => 110000, 'stock' => 45, 'description' => 'Kipas pendingin laptop gaming dengan lampu RGB keren.'],

            ['name' => 'Rak Bunga Susun Besi Minimalis', 'category' => 'Perlengkapan Rumah & Dekorasi', 'price' => 145000, 'stock' => 25, 'description' => 'Rak pot tanaman bahan besi kokoh anti karat, muat 5 pot.'],
            ['name' => 'Sprei Katun Lokal Motif Bunga Estetik', 'category' => 'Perlengkapan Rumah & Dekorasi', 'price' => 95000, 'stock' => 60, 'description' => 'Sprei ukuran Queen no.2, bahan adem tidak luntur.'],

            ['name' => 'Serum Wajah Niacinamide Pencerah Kulit', 'category' => 'Kecantikan & Perawatan Diri', 'price' => 65000, 'stock' => 100, 'description' => 'Serum lokal viral ampuh mencerahkan dan menghilangkan noda hitam.'],
            ['name' => 'Lip Tint Tahan Lama Water Based Lokal', 'category' => 'Kecantikan & Perawatan Diri', 'price' => 35000, 'stock' => 200, 'description' => 'Pewarna bibir tekstur air, ringan dan stain tahan lama seharian.'],

            ['name' => 'Masker Medis 3 Ply Earloop Isi 50', 'category' => 'Kesehatan & Medis', 'price' => 20000, 'stock' => 500, 'description' => 'Masker kesehatan standar kemenkes, nyaman dipakai sehari-hari.'],
            ['name' => 'Madu Murni Hutan Baduy 500ml', 'category' => 'Kesehatan & Medis', 'price' => 90000, 'stock' => 70, 'description' => 'Madu odeng asli dari hutan Baduy, menjaga imun tubuh.'],

            ['name' => 'Gendongan Bayi Depan Hipseat Ergonomis', 'category' => 'Ibu, Bayi & Anak', 'price' => 165000, 'stock' => 30, 'description' => 'Gendongan modern aman untuk tulang bayi dan nyaman untuk ibu.'],
            ['name' => 'Setelan Baju Tidur Anak Motif Dino', 'category' => 'Ibu, Bayi & Anak', 'price' => 45000, 'stock' => 80, 'description' => 'Piyama anak bahan kaos katun, menyerap keringat.'],

            ['name' => 'Matras Yoga Anti Slip Ketebalan 10mm', 'category' => 'Hobi & Olahraga', 'price' => 85000, 'stock' => 50, 'description' => 'Alas olahraga empuk, tidak licin, gratis tas jaring.'],
            ['name' => 'Jersey Sepeda Dry Fit Printing Custom', 'category' => 'Hobi & Olahraga', 'price' => 110000, 'stock' => 40, 'description' => 'Baju gowes bahan cepat kering, desain printing tajam.'],

            ['name' => 'Helm Bogo Retro Kaca Datar SNI', 'category' => 'Otomotif & Aksesoris', 'price' => 180000, 'stock' => 60, 'description' => 'Helm klasik kekinian standar SNI, busa bisa dilepas cuci.'],
            ['name' => 'Sarung Tangan Motor Touchscreen Anti Slip', 'category' => 'Otomotif & Aksesoris', 'price' => 35000, 'stock' => 100, 'description' => 'Sarung tangan riding bisa main HP tanpa dilepas.'],

            ['name' => "Novel Fiksi Metropop 'Senja di Jakarta'", 'category' => 'Buku & Alat Tulis', 'price' => 79000, 'stock' => 45, 'description' => 'Buku best seller tentang kehidupan romansa di ibu kota.'],
            ['name' => 'Paket Alat Tulis Sekolah Lengkap', 'category' => 'Buku & Alat Tulis', 'price' => 25000, 'stock' => 150, 'description' => 'Isi kotak pensil, pulpen, penghapus, penggaris, dan rautan.'],

            ['name' => 'Mukena Bali Rayon Adem Motif Bunga', 'category' => 'Perlengkapan Muslim', 'price' => 115000, 'stock' => 75, 'description' => 'Mukena jumbo bahan rayon premium, dingin dipakai ibadah.'],
            ['name' => 'Sarung Tenun Wadimor Motif Hujan Gerimis', 'category' => 'Perlengkapan Muslim', 'price' => 65000, 'stock' => 200, 'description' => 'Sarung tenun asli nyaman dipakai, motif elegan.'],
        ];

        $imagePaths = [
            'products/sample1.png',
            'products/sample2.png',
            'products/sample3.png',
        ];

        $created = 0;
        $sellerIndex = 0;
        foreach ($products as $idx => $p) {
            // assign seller in round-robin
            $seller = $sellers[$sellerIndex % $sellers->count()];

            // ensure category exists
            $category = Category::firstOrCreate(
                ['name' => $p['category']],
                ['slug' => Str::slug($p['category']), 'description' => $p['category']]
            );

            Product::create([
                'seller_id' => $seller->seller_id,
                'name' => $p['name'],
                'slug' => Str::slug($p['name']) . '-' . ($idx + 1),
                'description' => $p['description'],
                'category_id' => $category->category_id,
                'price' => $p['price'],
                'stock' => $p['stock'],
                // store as array so Eloquent cast('images' => 'array') will handle JSON encoding
                'images' => $imagePaths,
                'primary_image' => $imagePaths[0],
                'visitor' => rand(5, 200),
                'is_active' => true,
            ]);

            $created++;
            $sellerIndex++;
        }

        // Update sellers with valid location codes (keep existing helper)
        $this->updateSellersWithValidLocations();

        $this->command->info('ProductSeeder selesai! Total produk: ' . $created);
    }

    /**
     * Update sellers dengan valid location codes (province, city, district, village)
     */
    private function updateSellersWithValidLocations(): void
    {
        // Prepare an indexed list preserving province codes
        $locations = [];
        foreach ($this->validLocations as $prov => $loc) {
            $locations[] = [
                'province' => $prov,
                'city' => $loc[0] ?? null,
                'district' => $loc[1] ?? null,
                'village' => $loc[2][0] ?? null,
            ];
        }

        if (empty($locations)) {
            return;
        }

        $locationIndex = 0;

        // Only update sellers that don't yet have valid province_id (avoid repeated updates)
        Seller::whereNull('province_id')->orWhere('province_id', '')->get()->each(function ($seller) use (&$locationIndex, $locations) {
            $loc = $locations[$locationIndex % count($locations)];

            $seller->update([
                'province_id' => $loc['province'],
                'city_id' => $loc['city'],
                'district_id' => $loc['district'],
                'village_id' => $loc['village'],
            ]);

            $locationIndex++;
        });

        $this->command->info('Sellers location codes updated dengan valid format!');
    }

    private function generatePrice(): float
    {
        $prices = [
            49999,   // ~50K
            99999,   // ~100K
            149999,  // ~150K
            199999,  // ~200K
            299999,  // ~300K
            499999,  // ~500K
            749999,  // ~750K
            999999,  // ~1JT
            1499999, // ~1.5JT
            1999999, // ~2JT
        ];

        return $prices[array_rand($prices)];
    }
}

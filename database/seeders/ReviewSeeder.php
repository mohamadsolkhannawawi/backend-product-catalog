<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    private array $reviewerNames = [
        'Siti Nurhayati', 'Budi Santoso', 'Rini Handayani', 'Ahmad Wijaya',
        'Dewi Lestari', 'Joko Prabowo', 'Intan Kusuma', 'Hendra Gunawan',
        'Ratna Pertiwi', 'Bambang Suryanto', 'Lina Marlina', 'Dani Hermawan',
        'Wulandari Putri', 'Fajar Hidayat', 'Indah Suryanti', 'Kardi Supriyanto',
        'Mita Cahyani', 'Agus Prabowo', 'Yuni Kartini', 'Rina Marlina',
        'Tono Santosa', 'Sinta Rahayu', 'Utami Wijaya', 'Vicky Hermanto',
        'Wendy Kusuma', 'Xander Prabowo', 'Yuli Handayani', 'Zara Pertiwi',
    ];

    private array $positiveComments = [
        'Produk bagus, sesuai dengan deskripsi. Pengiriman cepat dan aman. Sangat puas dengan pembelian ini!',
        'Kualitas terbaik, harga masuk akal. Akan beli lagi di toko ini.',
        'Barang berkualitas tinggi, packaging rapi. Rekomendasi untuk semua orang!',
        'Puas sekali, produk sesuai harapan. Layanan pelanggan responsif dan helpful.',
        'Sangat rekomendasi! Kualitas premium dengan harga bersahabat.',
        'Beli sudah 5x, selalu puas. Toko terpercaya dan profesional.',
        'Produk original, harga kompetitif. Pengiriman tepat waktu!',
        'Kualitas exceeds expectations, very satisfied with this purchase.',
        'Barang sampai dengan aman dan cepat. Terima kasih untuk service terbaik!',
        'Mantap! Sesuai dengan foto. Akan menjadi pembeli setia toko ini.',
    ];

    private array $neutralComments = [
        'Produk OK, sesuai dengan yang dipesan. Harga standart untuk kualitas ini.',
        'Lumayan bagus, ada beberapa minor yang bisa diperbaiki.',
        'Cukup memuaskan, pengiriman berjalan dengan baik.',
        'Produk bagus tapi kemasan bisa lebih rapi lagi.',
        'Sesuai harapan, tidak ada yang mengecewakan.',
        'Produk standar kualitas, cocok untuk penggunaan harian.',
        'Baik, tidak ada keluhan berarti. Akan coba produk lain lagi.',
        'Decent quality, good value for money.',
        'Produk OK tapi ada sedikit defect, tapi masih acceptable.',
        'Cukup puas, mungkin review berikutnya bisa lebih baik.',
    ];

    private array $negativeComments = [
        'Produk kurang sesuai ekspektasi. Kualitasnya biasa saja.',
        'Ada bagian yang rusak saat pengiriman. Perlu packaging lebih baik.',
        'Produk OK tapi ada cacat pabrik yang mengganggu.',
        'Harga terlalu mahal untuk kualitas seperti ini.',
        'Pengiriman lama dan barang tidak sesuai deskripsi.',
        'Kualitas mengecewakan, tidak seperti foto.',
        'Barang sudah pernah dipakai sebelumnya. Tidak recommended.',
        'Ada yang hilang di dalam paket, pelayanan kurang responsif.',
        'Produk tidak tahan lama, rusak setelah beberapa hari.',
        'Disappointed with quality, expected much better.',
    ];

    private array $provinces = [
        '11', '12', '13', '14', '15', '16', '17', '18', '19', '21',
        '31', '32', '33', '34', '35', '36', '51', '52', '53', '61',
        '62', '63', '64', '65', '71', '72', '73', '74', '75', '76'
    ];

    private array $emails = [
        'customer1@gmail.com', 'reviewer2@yahoo.com', 'user3@outlook.com',
        'pembeli4@gmail.com', 'penilaian5@yahoo.com', 'produk6@outlook.com',
        'belanja7@gmail.com', 'toko8@yahoo.com', 'barang9@outlook.com',
        'review10@gmail.com', 'rating11@yahoo.com', 'komentar12@outlook.com',
        'pelanggan13@gmail.com', 'review14@yahoo.com', 'rating15@outlook.com',
        'comment16@gmail.com', 'feedback17@yahoo.com', 'opinion18@outlook.com',
        'buyer19@gmail.com', 'shopper20@yahoo.com', 'customer21@outlook.com',
        'consumer22@gmail.com', 'client23@yahoo.com', 'member24@outlook.com',
        'guest25@gmail.com', 'visitor26@yahoo.com', 'user27@outlook.com',
        'reviewer28@gmail.com', 'rater29@yahoo.com', 'commenter30@outlook.com',
    ];

    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('Pastikan ProductSeeder sudah dijalankan terlebih dahulu!');
            return;
        }

        $emailIndex = 0;
        $reviewCount = 0;

        foreach ($products as $product) {
            // Setiap produk mendapat 2 review seperti permintaan
            $numReviews = 2;

            for ($i = 0; $i < $numReviews; $i++) {
                $rating = rand(1, 5);
                
                // Pilih comment berdasarkan rating
                if ($rating >= 4) {
                    $comment = $this->positiveComments[array_rand($this->positiveComments)];
                } elseif ($rating === 3) {
                    $comment = $this->neutralComments[array_rand($this->neutralComments)];
                } else {
                    $comment = $this->negativeComments[array_rand($this->negativeComments)];
                }

                // Use email dengan index untuk memastikan unique per product
                $email = $this->emails[$emailIndex % count($this->emails)];
                $emailIndex++;

                Review::create([
                    'product_id' => $product->product_id,
                    'name' => $this->reviewerNames[array_rand($this->reviewerNames)],
                    'email' => $email,
                    'province_id' => $this->provinces[array_rand($this->provinces)],
                    'phone' => '08' . rand(10, 99) . rand(10000000, 99999999),
                    'rating' => $rating,
                    'comment' => $comment,
                ]);

                $reviewCount++;
            }
        }

        $this->command->info('ReviewSeeder selesai! Total review: ' . $reviewCount);
    }
}

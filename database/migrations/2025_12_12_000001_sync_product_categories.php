<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all products with their names and current categories
        $products = Product::all();
        
        // Mapping berdasarkan product seeder
        $productCategoryMapping = [
            'Tas Bahu (Shoulder Bag)' => 'fashion-aksesoris',
            'Speaker Bluetooth Portabel' => 'elektronik-gadget',
            'Smartphone Mid-range 128GB' => 'elektronik-gadget',
            'Set Cat Akrilik 12 Warna' => 'kerajinan-seni',
            'Rak Buku Minimalis' => 'rumah-dekorasi',
            'Pengharum Mobil Aroma Kopi' => 'otomotif',
            'Mobil Remote Control Off-Road' => 'hobi-olahraga',
            'Meja Kerja' => 'rumah-dekorasi',
            'Matras Yoga Anti-Slip' => 'hobi-olahraga',
            'Keripik Tempe Sagu 250g' => 'makanan-minuman',
            'Headphone Noise Cancelling' => 'elektronik-gadget',
            'Buku Filosofi Teras' => 'buku-alat-tulis',
        ];
        
        // Get all categories
        $categories = Category::all()->keyBy('slug');
        
        foreach ($products as $product) {
            // Check if product name matches known products
            if (isset($productCategoryMapping[$product->name])) {
                $categorySlug = $productCategoryMapping[$product->name];
                
                if (isset($categories[$categorySlug])) {
                    $category = $categories[$categorySlug];
                    
                    // Update product category_id
                    DB::table('products')
                        ->where('product_id', $product->product_id)
                        ->update(['category_id' => $category->category_id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot safely reverse this migration
        // To undo, manually update category_id to NULL for affected products
    }
};

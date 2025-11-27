<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "=== CHECKING ACTIVE PRODUCTS WITH SELLER ===\n";
$products = Product::with('seller.city', 'seller.province', 'category')
    ->withAvg('reviews', 'rating')
    ->where('is_active', true)
    ->select(
        'product_id',
        'name',
        'slug',
        'price',
        'stock',
        'category',
        'category_id',
        'images',
        'primary_image',
        'seller_id',
        'created_at'
    )
    ->get();

echo "Total products from query: " . $products->count() . "\n\n";

foreach ($products as $p) {
    echo "Product: {$p->name}\n";
    echo "  Seller ID: {$p->seller_id}\n";
    echo "  Seller: " . ($p->seller ? $p->seller->store_name : "NULL") . "\n";
    echo "  Seller Active: " . ($p->seller ? ($p->seller->is_active ? "YES" : "NO") : "N/A") . "\n";
    echo "  City: " . ($p->seller?->city?->name ?? "N/A") . "\n";
    echo "  Province: " . ($p->seller?->province?->name ?? "N/A") . "\n";
    echo "\n";
}

echo "=== CHECK SELLERS ===\n";
$sellers = DB::table('sellers')->get();
echo "Total sellers in DB: " . count($sellers) . "\n";
foreach ($sellers as $s) {
    echo "- Seller: {$s->store_name} (ID: " . substr($s->seller_id, 0, 8) . "..., Active: " . ($s->is_active ? "YES" : "NO") . ")\n";
}

?>

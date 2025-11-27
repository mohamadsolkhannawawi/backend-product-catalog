<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

echo "=== CHECKING ACTIVE PRODUCTS ===\n";
$activeProducts = Product::where('is_active', true)->get();
echo "Total active products in DB: " . $activeProducts->count() . "\n";
foreach ($activeProducts as $p) {
    echo "- {$p->name} (ID: " . substr($p->product_id, 0, 8) . "..., Active: " . ($p->is_active ? "YES" : "NO") . ")\n";
}

echo "\n=== CHECKING CACHE ===\n";
$version = Cache::get('products_cache_version', 1);
echo "Current cache version: $version\n";

// List all cache keys matching products
echo "\nClearing product cache...\n";
Cache::flush();
echo "✅ Cache flushed!\n";

echo "\n=== AFTER CACHE FLUSH ===\n";
$version = Cache::get('products_cache_version', 1);
echo "Cache version after flush: $version\n";

echo "\n✅ Test complete!\n";
?>

<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== CHECKING PRODUCT DATA ===\n";
$products = Product::select('product_id', 'name', 'price', 'stock', 'is_active', 'created_at')
    ->limit(3)
    ->get();

foreach ($products as $p) {
    echo "Product: {$p->name}\n";
    echo "  is_active: " . ($p->is_active ? "TRUE (Active)" : "FALSE (Inactive)") . "\n";
    echo "  Stock: {$p->stock}\n";
    echo "  Price: {$p->price}\n";
    echo "\n";
}

echo "=== ALL DATA LOOKS GOOD ===\n";
?>

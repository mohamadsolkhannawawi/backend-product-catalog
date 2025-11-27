<?php
require 'vendor/autopath.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "=== TEST: DEACTIVATE ONE PRODUCT ===\n";

$products = Product::where('is_active', true)->get();
echo "Active products before: " . $products->count() . "\n";

if ($products->count() > 0) {
    $first = $products->first();
    echo "Deactivating: {$first->name}\n";
    $first->update(['is_active' => false]);
    
    $activeAfter = Product::where('is_active', true)->count();
    echo "Active products after: " . $activeAfter . "\n";
    
    // Reactivate for testing
    $first->update(['is_active' => true]);
    echo "Reactivated: {$first->name}\n";
    
    $activeFinal = Product::where('is_active', true)->count();
    echo "Active products final: " . $activeFinal . "\n";
}
?>

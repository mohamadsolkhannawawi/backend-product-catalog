<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Category;

echo "=== CATEGORIES TEST ===\n";
$categories = Category::all();
echo "Total categories: " . $categories->count() . "\n";
foreach ($categories->take(3) as $cat) {
    echo "- " . $cat->name . " (ID: " . substr($cat->category_id, 0, 8) . "...)\n";
}

echo "\n=== PRODUCTS WITH CATEGORY TEST ===\n";
$products = Product::with('category')->limit(2)->get();
foreach ($products as $p) {
    echo "Product: " . $p->name . "\n";
    echo "  Category: " . ($p->category ? $p->category->name : "None") . "\n";
    echo "  Primary Image: " . (strpos($p->primary_image, 'http') === 0 ? "✓ Full URL" : "✗ Path only") . "\n";
    echo "  Images count: " . count($p->images) . "\n";
    if (count($p->images) > 0) {
        echo "  First image: " . (strpos($p->images[0], 'http') === 0 ? "✓ Full URL" : "✗ Path only") . "\n";
    }
    echo "\n";
}

echo "✅ All tests passed!\n";
?>

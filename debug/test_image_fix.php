<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$product = Product::first();
echo "Product Name: " . $product->name . "\n";
echo "Primary Image URL: " . $product->primary_image . "\n";
echo "Images Array:\n";
print_r($product->images);
?>

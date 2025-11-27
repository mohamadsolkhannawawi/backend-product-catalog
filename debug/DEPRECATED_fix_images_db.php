<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

// Fix all products with incorrect primary_image paths
$products = Product::all();
foreach ($products as $product) {
    $fixed = false;
    
    // Fix primary_image
    if ($product->primary_image && str_starts_with($product->primary_image, 'http')) {
        // Extract just the relative path
        // From: "http://localhost:8000/storage//storage/products/..."
        // To: "products/..."
        $image = str_replace('http://localhost:8000/storage/', '', $product->primary_image);
        $image = ltrim($image, '/');
        if ($image !== $product->getRawOriginal()['primary_image']) {
            DB::table('products')->where('product_id', $product->product_id)
                ->update(['primary_image' => $image]);
            $fixed = true;
        }
    }
    
    // Fix images array
    if ($product->images && is_array($product->images)) {
        $newImages = array_map(function ($img) {
            if (str_starts_with($img, 'http')) {
                return str_replace('http://localhost:8000/storage/', '', $img);
            }
            return ltrim($img, '/');
        }, $product->images);
        
        if ($newImages !== $product->images) {
            DB::table('products')->where('product_id', $product->product_id)
                ->update(['images' => json_encode($newImages)]);
            $fixed = true;
        }
    }
    
    if ($fixed) {
        echo "Fixed product: " . $product->name . "\n";
    }
}

echo "Done\n";
?>

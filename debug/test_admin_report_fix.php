<?php
/**
 * Test: Admin top-rated-products report PDF generation
 * This script tests the fix for the missing lv_provinces table issue
 */

// Bootstrap Laravel
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Use Laravel Query Builder directly
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Review;

echo "=== Testing Admin Top-Rated-Products Report ===\n\n";

// Test 1: Check if products exist
$productCount = Product::count();
echo "1. Total products in DB: {$productCount}\n";

// Test 2: Check if reviews exist
$reviewCount = Review::count();
echo "2. Total reviews in DB: {$reviewCount}\n";

// Test 3: Try to fetch products with relationships (the query used in controller)
try {
    $data = Product::with(['seller:seller_id,store_name', 'category:category_id,name'])
        ->select('products.product_id','products.name','products.category_id','products.price','products.seller_id', DB::raw('COALESCE(AVG(reviews.rating),0) as avg_rating'))
        ->leftJoin('reviews', 'reviews.product_id', '=', 'products.product_id')
        ->where('products.is_active', true)
        ->groupBy('products.product_id','products.name','products.category_id','products.price','products.seller_id')
        ->orderByDesc('avg_rating')
        ->limit(3)
        ->get();

    echo "3. Successfully fetched " . count($data) . " top-rated products\n";
    
    foreach ($data as $idx => $product) {
        echo "\n   Product " . ($idx + 1) . ":\n";
        echo "   - Name: " . $product->name . "\n";
        echo "   - Price: Rp " . number_format($product->price) . "\n";
        echo "   - Avg Rating: " . number_format($product->avg_rating, 1) . "\n";
        echo "   - Store: " . ($product->seller?->store_name ?? 'N/A') . "\n";
        echo "   - Category: " . ($product->category?->name ?? 'N/A') . "\n";
        
        // Test adding reviewer province (this is what the controller now does)
        $reviewerProvince = Review::where('product_id', $product->product_id)
            ->orderByDesc('created_at')
            ->value('province_id') ?? 'N/A';
        echo "   - Latest Reviewer Province: " . $reviewerProvince . "\n";
    }
    
    echo "\n4. ✓ Query and data retrieval working!\n";
    
} catch (\Exception $e) {
    echo "3. ✗ Error fetching products: " . $e->getMessage() . "\n";
    echo "   Full trace:\n";
    echo $e->getTraceAsString() . "\n";
}

// Test 5: Try to render the Blade view (simulating PDF generation without Dompdf)
echo "\n5. Testing Blade view rendering (without PDF)...\n";
try {
    $data = Product::with(['seller:seller_id,store_name', 'category:category_id,name'])
        ->select('products.product_id','products.name','products.category_id','products.price','products.seller_id', DB::raw('COALESCE(AVG(reviews.rating),0) as avg_rating'))
        ->leftJoin('reviews', 'reviews.product_id', '=', 'products.product_id')
        ->where('products.is_active', true)
        ->groupBy('products.product_id','products.name','products.category_id','products.price','products.seller_id')
        ->orderByDesc('avg_rating')
        ->limit(5)
        ->get();

    // Add reviewer province to each product
    $data->each(function ($product) {
        $reviewerProvince = Review::where('product_id', $product->product_id)
            ->orderByDesc('created_at')
            ->value('province_id') ?? 'N/A';
        $product->reviewer_province = $reviewerProvince;
    });

    // Try to render view
    $html = view('pdf.admin-top-rated-products', ['data' => $data])->render();
    echo "   ✓ Blade view rendered successfully!\n";
    echo "   HTML length: " . strlen($html) . " characters\n";
    
    // Check if HTML contains expected table
    if (strpos($html, '<table>') !== false && strpos($html, '</table>') !== false) {
        echo "   ✓ Table found in HTML\n";
    }
    
    if (preg_match_all('/<tr>/', $html) > 0) {
        $rowCount = preg_match_all('/<tr>/', $html) - 1; // Subtract header row
        echo "   ✓ Found " . $rowCount . " data rows in table\n";
    }
    
} catch (\Exception $e) {
    echo "   ✗ Error rendering Blade view: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
?>

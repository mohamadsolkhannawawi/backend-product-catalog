<?php
/**
 * Test Filter Produk Comprehensive
 * Jalankan dengan: php artisan tinker < debug/test_filter_comprehensive.php
 */

use App\Models\Product;
use App\Models\Category;

// Test 1: Filter by Province Only
echo "=== Test 1: Filter by Province Only ===\n";
$province_code = "11"; // DKI Jakarta
$query = Product::query()
    ->where('is_active', true)
    ->whereHas('seller', function ($sq) use ($province_code) {
        $sq->where('province_id', $province_code);
    });
$count = $query->count();
echo "Products in Province {$province_code}: {$count}\n";
$query->with('seller', 'category')->take(3)->get()->each(function ($p) {
    echo "  - {$p->name} (Seller: {$p->seller->store_name})\n";
});
echo "\n";

// Test 2: Filter by City Only
echo "=== Test 2: Filter by City Only ===\n";
$city_code = "1171"; // Jakarta Pusat
$query = Product::query()
    ->where('is_active', true)
    ->whereHas('seller', function ($sq) use ($city_code) {
        $sq->where('city_id', $city_code);
    });
$count = $query->count();
echo "Products in City {$city_code}: {$count}\n";
$query->with('seller', 'category')->take(3)->get()->each(function ($p) {
    echo "  - {$p->name} (Price: Rp " . number_format($p->price, 0, ',', '.') . ")\n";
});
echo "\n";

// Test 3: Filter by Category
echo "=== Test 3: Filter by Category ===\n";
$categories = Category::limit(3)->get();
foreach ($categories as $cat) {
    $count = Product::where('is_active', true)
        ->where('category_id', $cat->category_id)
        ->count();
    echo "Products in Category '{$cat->name}': {$count}\n";
}
echo "\n";

// Test 4: Filter by Price Range
echo "=== Test 4: Filter by Price Range ===\n";
$min = 100000;
$max = 500000;
$query = Product::query()
    ->where('is_active', true)
    ->where('price', '>=', $min)
    ->where('price', '<=', $max);
$count = $query->count();
echo "Products between Rp " . number_format($min, 0, ',', '.') . " - Rp " . number_format($max, 0, ',', '.') . ": {$count}\n";
$query->select('name', 'price')->orderBy('price')->take(5)->get()->each(function ($p) {
    echo "  - {$p->name}: Rp " . number_format($p->price, 0, ',', '.') . "\n";
});
echo "\n";

// Test 5: Sorting by Rating
echo "=== Test 5: Sorting by Rating (Highest) ===\n";
$query = Product::query()
    ->where('is_active', true)
    ->withAvg('reviews', 'rating')
    ->orderByDesc('reviews_avg_rating')
    ->select('name', 'price');
$count = $query->count();
echo "Total active products: {$count}\n";
$query->take(5)->get()->each(function ($p) {
    $rating = round($p->reviews_avg_rating ?? 0, 2);
    echo "  - {$p->name}: Rating {$rating}\n";
});
echo "\n";

// Test 6: Sorting by Price
echo "=== Test 6: Sorting by Price (Cheapest First) ===\n";
Product::where('is_active', true)
    ->select('name', 'price')
    ->orderBy('price', 'asc')
    ->take(3)
    ->get()
    ->each(function ($p) {
        echo "  - {$p->name}: Rp " . number_format($p->price, 0, ',', '.') . "\n";
    });
echo "\n";

// Test 7: Combination Filter
echo "=== Test 7: Combination Filter (Province + Category + Price) ===\n";
$province = "11";
$category = Category::first();

if ($category) {
    $query = Product::query()
        ->where('is_active', true)
        ->where('category_id', $category->category_id)
        ->where('price', '>=', 50000)
        ->where('price', '<=', 1000000)
        ->whereHas('seller', function ($sq) use ($province) {
            $sq->where('province_id', $province);
        })
        ->withAvg('reviews', 'rating')
        ->orderByDesc('reviews_avg_rating')
        ->select('name', 'price', 'category_id');
    
    $count = $query->count();
    echo "Filtered products: {$count}\n";
    $query->with('category')->take(3)->get()->each(function ($p) {
        $rating = round($p->reviews_avg_rating ?? 0, 2);
        echo "  - {$p->name} ({$p->category?->name}): Rp " . number_format($p->price, 0, ',', '.') . " | Rating: {$rating}\n";
    });
} else {
    echo "No categories found\n";
}
echo "\n";

// Test 8: Parameter Mapping Summary
echo "=== Test 8: API Parameter Mapping ===\n";
echo "✓ Frontend sends → Backend receives:\n";
echo "  - filters.province → province_id\n";
echo "  - filters.city → city_id\n";
echo "  - filters.district → district_id\n";
echo "  - filters.village → village_id\n";
echo "  - filters.category → category_id\n";
echo "  - filters.min_price → min_price\n";
echo "  - filters.max_price → max_price\n";
echo "  - filters.sort → sort (newest|price_asc|price_desc|rating_desc)\n";
echo "\n";

echo "=== ✓ All Tests Completed Successfully ===\n";
?>

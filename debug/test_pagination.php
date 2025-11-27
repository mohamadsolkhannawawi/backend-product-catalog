<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ProductPublicController;

echo "=== TEST PAGINATION & PRODUCT COUNT ===\n";

$controller = new ProductPublicController();

// Test page 1
$request1 = Request::create('/catalog?page=1', 'GET');
$response1 = $controller->index($request1);
$data1 = json_decode($response1->getContent(), true);

echo "PAGE 1:\n";
echo "- Products count: " . count($data1['data'] ?? []) . "\n";
echo "- Total in DB: " . ($data1['total'] ?? 0) . "\n";
echo "- Current page: " . ($data1['current_page'] ?? 0) . "\n";
echo "- Last page: " . ($data1['last_page'] ?? 0) . "\n";
echo "- Products:\n";
foreach ($data1['data'] ?? [] as $p) {
    echo "  * " . $p['name'] . "\n";
}

// Test page 2
$request2 = Request::create('/catalog?page=2', 'GET');
$response2 = $controller->index($request2);
$data2 = json_decode($response2->getContent(), true);

echo "\nPAGE 2:\n";
echo "- Products count: " . count($data2['data'] ?? []) . "\n";
echo "- Products:\n";
foreach ($data2['data'] ?? [] as $p) {
    echo "  * " . $p['name'] . "\n";
}

echo "\n✅ If you see 2 products total on page 1, everything is working!\n";
?>

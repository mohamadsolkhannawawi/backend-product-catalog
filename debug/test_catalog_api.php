<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ProductPublicController;

echo "=== SIMULATING API CALL TO /catalog ===\n";

$controller = new ProductPublicController();
$request = Request::create('/catalog', 'GET');

$response = $controller->index($request);
$data = json_decode($response->getContent(), true);

echo "Response status: " . $response->status() . "\n";
echo "Total items in response: " . count($data['data'] ?? []) . "\n";
echo "Products returned:\n";

foreach ($data['data'] ?? [] as $idx => $p) {
    echo ($idx + 1) . ". " . $p['name'] . " (Seller: " . $p['seller_id'] . ", Active: " . ($p['is_active'] ?? 'N/A') . ")\n";
}

echo "\nFull response structure:\n";
print_r([
    'current_page' => $data['current_page'] ?? null,
    'total' => $data['total'] ?? null,
    'last_page' => $data['last_page'] ?? null,
    'per_page' => $data['per_page'] ?? null,
]);

?>

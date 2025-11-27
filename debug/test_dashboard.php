<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Get first seller
$seller = Seller::first();
if (!$seller) {
    echo "No seller found\n";
    exit;
}

$user = $seller->user;
echo "Email: " . $user->email . "\n";
echo "User ID: " . $user->user_id . "\n";
echo "Role: " . $user->role . "\n";
echo "Seller ID: " . $seller->seller_id . "\n";
echo "Seller Status: " . $seller->status . "\n";
echo "Seller Is Active: " . $seller->is_active . "\n";

// Generate token
$token = $user->createToken('test')->plainTextToken;
echo "\nGenerated token:\n";
echo $token . "\n";

// Test if route exists
$routes = Route::getRoutes();
$dashboardRoutes = $routes->getRoutesByName();
echo "\nRoutes containing 'dashboard.seller':\n";
foreach ($routes as $route) {
    if (strpos($route->uri, 'dashboard/seller') !== false) {
        echo "  " . $route->methods[0] . " " . $route->uri . "\n";
    }
}
?>

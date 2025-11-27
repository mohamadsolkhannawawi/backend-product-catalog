<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$p = Product::first();
echo "primary_image raw: " . var_export($p->primary_image, true) . "\n";
echo "images raw: " . var_export($p->images, true) . "\n";
?>

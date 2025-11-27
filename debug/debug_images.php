<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::with('seller')->limit(5)->get();
foreach ($products as $p) {
    echo "Product: {$p->name}\n";
    echo "  Primary Image: " . var_export($p->primary_image, true) . "\n";
    echo "  Images: " . var_export($p->images, true) . "\n";
    echo "  Via Accessor - Primary: " . var_export($p->getRawOriginal('primary_image'), true) . "\n";
    echo "  Via Accessor - Images: " . var_export($p->getRawOriginal('images'), true) . "\n";
    echo "\n";
}
?>

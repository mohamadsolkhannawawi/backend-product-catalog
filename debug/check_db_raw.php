<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$products = DB::table('products')->select('product_id', 'name', 'images', 'primary_image')->limit(3)->get();
foreach ($products as $p) {
    echo "Product ID: {$p->product_id}\n";
    echo "Name: {$p->name}\n";
    echo "RAW images column: ";
    var_dump($p->images);
    echo "RAW primary_image column: ";
    var_dump($p->primary_image);
    echo "\n";
}
?>

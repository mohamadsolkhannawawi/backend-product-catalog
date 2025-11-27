<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

$p = Product::first();
echo "Via model accessor: " . var_export($p->primary_image, true) . "\n";
echo "Raw from DB: " . var_export($p->getRawOriginal('primary_image'), true) . "\n";

// Check actual DB row
$row = DB::table('products')->where('product_id', $p->product_id)->first();
echo "DB row primary_image: " . var_export($row->primary_image, true) . "\n";
?>

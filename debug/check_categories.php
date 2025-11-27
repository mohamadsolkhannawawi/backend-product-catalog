<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

$categories = Category::all();
echo "Total categories: " . $categories->count() . "\n";
foreach ($categories as $cat) {
    echo "- " . $cat->name . "\n";
}
?>

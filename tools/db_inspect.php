<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public'");
echo "Tables in public schema:\n";
foreach ($tables as $t) {
    $arr = (array) $t;
    echo $arr['tablename'] . "\n";
}

echo "\nRows in indonesia_provinces:\n";
try {
    $rows = DB::table('indonesia_provinces')->get();
    echo 'count = ' . $rows->count() . "\n";
    foreach ($rows as $r) {
        echo $r->code . ' - ' . $r->name . "\n";
    }
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

echo "\nRows in provinces:\n";
try {
    $rows2 = DB::table('provinces')->get();
    echo 'count = ' . $rows2->count() . "\n";
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

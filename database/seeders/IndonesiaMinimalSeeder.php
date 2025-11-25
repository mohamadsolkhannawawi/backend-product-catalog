<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndonesiaMinimalSeeder extends Seeder
{
    public function run()
    {
        $prefix = config('laravolt.indonesia.table_prefix') ?: 'indonesia_';

        DB::table($prefix . 'provinces')->insertOrIgnore([
            ['code' => '12', 'name' => 'Test Province', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table($prefix . 'cities')->insertOrIgnore([
            ['code' => '1201', 'province_code' => '12', 'name' => 'Test City', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table($prefix . 'districts')->insertOrIgnore([
            ['code' => '120101', 'city_code' => '1201', 'name' => 'Test District', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table($prefix . 'villages')->insertOrIgnore([
            // tests expect a 10-digit village code
            ['code' => '1201010001', 'district_code' => '120101', 'name' => 'Test Village', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

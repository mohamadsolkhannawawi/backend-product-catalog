<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop temporary 'uuid' columns added during intermediate migrations if they still exist.
        $tables = ['users', 'sellers', 'products', 'reviews'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropColumn('uuid');
                });
            }
        }
    }

    public function down(): void
    {
        // No-op: re-adding the exact previous UUID values is not safe here.
    }
};

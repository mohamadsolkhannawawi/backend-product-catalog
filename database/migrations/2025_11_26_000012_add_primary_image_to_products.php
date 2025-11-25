<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        if (! Schema::hasColumn('products', 'primary_image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('primary_image')->nullable()->after('images');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'primary_image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('primary_image');
            });
        }
    }
};

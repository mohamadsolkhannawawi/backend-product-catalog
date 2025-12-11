<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'status')) {
            Schema::table('products', function (Blueprint $table) {
                $table->enum('status', ['draft', 'active', 'inactive', 'discontinued'])->default('draft')->after('stock');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'status')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        // Add phone column if it doesn't exist (nullable)
        if (! Schema::hasColumn('reviews', 'phone')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('phone', 32)->nullable();
            });
        }

        // Create unique indexes if not exists (Postgres compatible)
        // product_id + email
        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS reviews_product_email_unique ON reviews (product_id, email)");

        // product_id + phone (only if phone column exists)
        if (Schema::hasColumn('reviews', 'phone')) {
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS reviews_product_phone_unique ON reviews (product_id, phone)");
        }
    }

    public function down(): void
    {
        // Drop indexes if exist
        DB::statement("DROP INDEX IF EXISTS reviews_product_email_unique");
        DB::statement("DROP INDEX IF EXISTS reviews_product_phone_unique");

        // optionally drop phone column
        if (Schema::hasColumn('reviews', 'phone')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }
};

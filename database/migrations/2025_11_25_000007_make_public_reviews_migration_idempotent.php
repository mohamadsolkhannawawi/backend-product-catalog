<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This migration makes the old "update_reviews_table_for_public_reviews" idempotent
     * by checking if user_id column exists before dropping it.
     * 
     * This is needed because:
     * 1. The original migration unconditionally drops user_id without checking if it exists
     * 2. In test environment with SQLite, migrations run fresh and user_id may not exist
     * 3. We need to ensure the migration is safe for all scenarios (SQLite, MySQL, Postgres)
     */
    public function up(): void
    {
        if (Schema::hasColumn('reviews', 'user_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('reviews', 'user_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->char('user_id', 36)->nullable()->after('review_id');
            });
        }
    }
};

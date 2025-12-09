<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Change is_active default from true to false.
     * Only new sellers will have is_active=false by default.
     * Existing sellers keep their current is_active status (no data migration needed).
     */
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Drop the old constraint and recreate with new default
            $table->boolean('is_active')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Revert to old default
            $table->boolean('is_active')->default(true)->change();
        });
    }
};

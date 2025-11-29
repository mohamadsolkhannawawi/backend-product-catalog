<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // First, update all NULL values to empty string (or use user name as fallback)
            \DB::statement("UPDATE sellers SET pic_name = COALESCE(pic_name, '') WHERE pic_name IS NULL");
            
            // Then change pic_name to NOT nullable - it's required
            $table->string('pic_name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Revert to nullable if needed
            $table->string('pic_name')->nullable()->change();
        });
    }
};

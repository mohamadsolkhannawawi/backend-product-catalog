<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing category that used the old long name/slug
        DB::table('categories')
            ->where('slug', 'bahan-lokal-agro')
            ->orWhere('name', 'Bahan Lokal & Agro (Pertanian & Makanan Olahan)')
            ->update([
                'name' => 'Pertanian & Makanan Olahan',
                'slug' => 'pertanian-makanan-olahan',
                'description' => 'Pertanian & Makanan Olahan',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert change (best-effort) by changing slug/name back to original long form
        DB::table('categories')
            ->where('slug', 'pertanian-makanan-olahan')
            ->where('name', 'Pertanian & Makanan Olahan')
            ->update([
                'name' => 'Bahan Lokal & Agro (Pertanian & Makanan Olahan)',
                'slug' => 'bahan-lokal-agro',
                'description' => 'Bahan Lokal & Agro (Pertanian & Makanan Olahan)',
            ]);
    }
};

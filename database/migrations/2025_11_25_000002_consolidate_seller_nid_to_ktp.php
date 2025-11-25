<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sellers')) {
            return;
        }

        // 1) nid_number -> ktp_number
        if (Schema::hasColumn('sellers', 'nid_number')) {
            if (! Schema::hasColumn('sellers', 'ktp_number')) {
                // simple rename
                Schema::table('sellers', function (Blueprint $table) {
                    $table->renameColumn('nid_number', 'ktp_number');
                });
            } else {
                // both exist: copy values where ktp is null, then drop old column
                DB::statement("UPDATE sellers SET ktp_number = nid_number WHERE ktp_number IS NULL AND nid_number IS NOT NULL");
                Schema::table('sellers', function (Blueprint $table) {
                    $table->dropColumn('nid_number');
                });
            }
        }

        // 2) nid_image_path -> ktp_file_path
        if (Schema::hasColumn('sellers', 'nid_image_path')) {
            if (! Schema::hasColumn('sellers', 'ktp_file_path')) {
                Schema::table('sellers', function (Blueprint $table) {
                    $table->renameColumn('nid_image_path', 'ktp_file_path');
                });
            } else {
                DB::statement("UPDATE sellers SET ktp_file_path = nid_image_path WHERE ktp_file_path IS NULL AND nid_image_path IS NOT NULL");
                Schema::table('sellers', function (Blueprint $table) {
                    $table->dropColumn('nid_image_path');
                });
            }
        }
    }

    public function down(): void
    {
        // Reverting this automatically is tricky; do manually if needed in dev.
    }
};

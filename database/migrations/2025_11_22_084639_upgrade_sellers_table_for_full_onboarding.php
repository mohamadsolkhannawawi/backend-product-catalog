<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Deskripsi singkat toko
            $table->text('store_description')->nullable()->after('store_name');

            // RT/RW
            $table->string('rt', 5)->nullable()->after('address');
            $table->string('rw', 5)->nullable()->after('rt');

            // No KTP & path file
            $table->string('ktp_number', 16)->nullable()->after('nid_number');
            $table->string('ktp_file_path')->nullable()->after('nid_image_path');

            // Foto PIC (public)
            $table->string('pic_file_path')->nullable()->after('ktp_file_path');

            // Untuk proses verifikasi admin (SRS-02)
            $table->text('rejection_reason')->nullable()->after('status');
            $table->timestamp('verified_at')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn([
                'store_description',
                'rt',
                'rw',
                'ktp_number',
                'ktp_file_path',
                'pic_file_path',
                'rejection_reason',
                'verified_at',
            ]);
        });
    }
};

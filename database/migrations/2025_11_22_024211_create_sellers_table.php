<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('store_name');
            $table->string('phone')->nullable();

            $table->string('nid_number')->nullable(); // Nomor KTP
            $table->string('nid_image_path')->nullable(); // path file upload

            // Kolom wilayah (Laravolt Indonesia)
            $table->string('province_id', 10)->nullable();
            $table->string('city_id', 10)->nullable();
            $table->string('district_id', 10)->nullable();
            $table->string('village_id', 10)->nullable();

            $table->text('address')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seller_rejection_audits')) {
            return;
        }

        Schema::create('seller_rejection_audits', function (Blueprint $table) {
            $table->id();
            $table->string('seller_id')->nullable();
            $table->string('user_id')->nullable();
            $table->text('reason')->nullable();
            $table->json('seller_snapshot')->nullable();
            $table->json('user_snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_rejection_audits');
    }
};

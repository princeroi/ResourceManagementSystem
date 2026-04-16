<?php
// database/migrations/xxxx_xx_xx_create_sme_purchase_order_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sme_purchase_order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sme_purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');           // created, approved, rejected, attach_dr
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->json('note')->nullable();   // stock before/after, items, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sme_purchase_order_logs');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── sme_restocks ──────────────────────────────────────────────────
        Schema::create('sme_restocks', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('ordered_by');
            $table->date('ordered_at');
            $table->enum('status', ['pending', 'partial', 'delivered', 'cancelled'])->default('pending');
            $table->date('pending_at')->nullable();
            $table->date('partial_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->date('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── sme_restock_items ─────────────────────────────────────────────
        Schema::create('sme_restock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sme_restock_id')->constrained('sme_restocks')->cascadeOnDelete();
            $table->foreignId('sme_item_id')->constrained('sme_items')->cascadeOnDelete();
            $table->foreignId('sme_item_variant_id')->constrained('sme_item_variants')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('delivered_quantity')->default(0);
            $table->unsignedInteger('remaining_quantity')->default(0);
            $table->timestamps();
        });

        // ── sme_restock_logs ──────────────────────────────────────────────
        Schema::create('sme_restock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sme_restock_id')->constrained('sme_restocks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sme_restock_logs');
        Schema::dropIfExists('sme_restock_items');
        Schema::dropIfExists('sme_restocks');
    }
};
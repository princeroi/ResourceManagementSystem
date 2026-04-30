<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── office_supply_restocks ─────────────────────────────────────────
        Schema::create('office_supply_restocks', function (Blueprint $table) {
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

        // ── office_supply_restock_items ────────────────────────────────────
        Schema::create('office_supply_restock_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('office_supply_restock_id');
            $table->foreign('office_supply_restock_id', 'osri_restock_fk')
                ->references('id')->on('office_supply_restocks')->cascadeOnDelete();

            $table->unsignedBigInteger('office_supply_item_id');
            $table->foreign('office_supply_item_id', 'osri_item_fk')
                ->references('id')->on('office_supply_items')->cascadeOnDelete();

            $table->unsignedBigInteger('office_supply_item_variant_id');
            $table->foreign('office_supply_item_variant_id', 'osri_variant_fk')
                ->references('id')->on('office_supply_item_variants')->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('delivered_quantity')->default(0);
            $table->unsignedInteger('remaining_quantity')->default(0);
            $table->timestamps();
        });

        // ── office_supply_restock_logs ─────────────────────────────────────
        Schema::create('office_supply_restock_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('office_supply_restock_id');
            $table->foreign('office_supply_restock_id', 'osrl_restock_fk')
                ->references('id')->on('office_supply_restocks')->cascadeOnDelete();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'osrl_user_fk')
                ->references('id')->on('users')->cascadeOnDelete();

            $table->string('action');
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_supply_restock_logs');
        Schema::dropIfExists('office_supply_restock_items');
        Schema::dropIfExists('office_supply_restocks');
    }
};
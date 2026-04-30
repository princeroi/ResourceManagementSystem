<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── return_office_supply_items ─────────────────────────────────────
        Schema::create('return_office_supply_items', function (Blueprint $table) {
            $table->id();
            $table->string('returned_by');
            $table->string('received_by');
            $table->enum('status', ['pending', 'partial', 'returned', 'cancelled'])->default('pending');
            $table->date('pending_at')->nullable();
            $table->date('partial_at')->nullable();
            $table->date('returned_at')->nullable();
            $table->date('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── return_office_supply_item_lines ────────────────────────────────
        Schema::create('return_office_supply_item_lines', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('return_office_supply_item_id');
            $table->foreign('return_office_supply_item_id', 'rosil_return_fk')
                ->references('id')->on('return_office_supply_items')->cascadeOnDelete();

            $table->unsignedBigInteger('office_supply_item_id');
            $table->foreign('office_supply_item_id', 'rosil_item_fk')
                ->references('id')->on('office_supply_items')->cascadeOnDelete();

            $table->unsignedBigInteger('office_supply_item_variant_id');
            $table->foreign('office_supply_item_variant_id', 'rosil_variant_fk')
                ->references('id')->on('office_supply_item_variants')->cascadeOnDelete();

            $table->string('employee_name')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('returned_quantity')->default(0);
            $table->unsignedInteger('remaining_quantity')->default(0);
            $table->string('condition')->default('good');
            $table->string('reason')->nullable();
            $table->boolean('add_to_stock')->default(true);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // ── return_office_supply_item_logs ─────────────────────────────────
        Schema::create('return_office_supply_item_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('return_office_supply_item_id');
            $table->foreign('return_office_supply_item_id', 'roslog_return_fk')
                ->references('id')->on('return_office_supply_items')->cascadeOnDelete();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'roslog_user_fk')
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
        Schema::dropIfExists('return_office_supply_item_logs');
        Schema::dropIfExists('return_office_supply_item_lines');
        Schema::dropIfExists('return_office_supply_items');
    }
};
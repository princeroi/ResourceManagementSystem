<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_uniform_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('uniform_issuance_id')
                ->nullable()
                ->constrained('uniform_issuances')
                ->nullOnDelete();

            $table->foreignId('site_id')
                ->nullable()
                ->constrained('sites')
                ->nullOnDelete();

            $table->string('returned_by');
            $table->string('received_by');
            $table->text('notes')->nullable();

            $table->string('status')->default('pending'); // pending | partial | returned | cancelled

            $table->date('pending_at')->nullable();
            $table->date('partial_at')->nullable();
            $table->date('returned_at')->nullable();
            $table->date('cancelled_at')->nullable();

            $table->timestamps();
        });

        Schema::create('return_uniform_item_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_uniform_item_id')
                ->constrained('return_uniform_items')
                ->cascadeOnDelete();

            $table->foreignId('uniform_item_id')
                ->constrained('uniform_items')
                ->cascadeOnDelete();

            $table->foreignId('uniform_item_variant_id')
                ->constrained('uniform_item_variants')
                ->cascadeOnDelete();

            $table->foreignId('uniform_issuance_item_id')
                ->nullable()
                ->constrained('uniform_issuance_items')
                ->nullOnDelete();

            $table->string('employee_name')->nullable();
            $table->string('condition')->default('good'); // good | damaged | defective
            $table->string('reason')->nullable();
            $table->text('remarks')->nullable();

            // ── Stock flag ─────────────────────────────────────────────
            $table->boolean('add_to_stock')->default(true);
            // true  = when accepted, increment uniform_item_variants.uniform_item_quantity
            // false = record only, do NOT touch inventory

            $table->integer('quantity')->default(0);
            $table->integer('returned_quantity')->default(0);
            $table->integer('remaining_quantity')->default(0);

            $table->timestamps();
        });

        Schema::create('return_uniform_item_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_uniform_item_id')
                ->constrained('return_uniform_items')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action');
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_uniform_item_logs');
        Schema::dropIfExists('return_uniform_item_lines');
        Schema::dropIfExists('return_uniform_items');
    }
};
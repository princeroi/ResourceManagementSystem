<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('return_sme_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sme_purchase_order_id')
                ->nullable()
                ->constrained('sme_purchase_orders')
                ->nullOnDelete();
            $table->foreignId('site_id')
                ->nullable()
                ->constrained('sites')
                ->nullOnDelete();
            $table->string('returned_by');
            $table->string('received_by');
            $table->text('notes')
                ->nullable();
            $table->string('status')
                ->default('pending'); // pending | partial | returned | cancelled
            $table->date('pending_at')
                ->nullable();
            $table->date('partial_at')
                ->nullable();
            $table->date('returned_at')
                ->nullable();
            $table->date('cancelled_at')
                ->nullable();
            $table->timestamps();
        });

        Schema::create('return_sme_item_lines', function (Blueprint $table){
            $table->id();
            $table->foreignId('return_sme_item_id')
                ->constrained('return_sme_items')
                ->cascadeOnDelete();

            $table->foreignId('sme_item_id')
                ->constrained('sme_items')
                ->cascadeOnDelete();

            $table->foreignId('sme_item_variant_id')
                ->constrained('sme_item_variants')
                ->cascadeOnDelete();

            $table->foreignId('sme_purchase_order_item_id')
                ->nullable()
                ->constrained('sme_purchase_order_items')
                ->nullOnDelete();

            $table->string('employee_name')
                ->nullable();
            $table->string('condition')
                ->default('good'); // good | damaged | defective
            $table->string('reason')
                ->nullable();
            $table->text('remarks')
                ->nullable();

            $table->boolean('add_to_stock')
                ->default(true);

            $table->integer('quantity')
                ->default(0);
            $table->integer('returned_quantity')
                ->default(0);
            $table->integer('remaining_quantity')
                ->default(0);

            $table->timestamps();
        });

        Schema::create('return_sme_item_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_sme_item_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('action');
            $table->string('status_from')
                ->nullable();
            $table->string('status_to')
                ->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_sme_item_logs');
        Schema::dropIfExists('return_sme_item_lines');
        Schema::dropIfExists('return_sme_items');
    }
};

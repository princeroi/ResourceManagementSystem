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
        Schema::create('sme_purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sme_purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sme_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sme_item_variant_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->integer('released_quantity')->default(0);
            $table->integer('remaining_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sme_purchase_order_items');
    }
};

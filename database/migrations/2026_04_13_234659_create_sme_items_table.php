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
        Schema::create('sme_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sme_category_id')->constrained();
            $table->string('sme_item_name');
            $table->string('sme_item_brand')->nullable();
            $table->text('sme_item_description')->nullable();
            $table->decimal('sme_item_price', 15, 2)->nullable();
            $table->string('sme_item_image')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sme_items');
    }
};

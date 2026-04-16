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
        Schema::create('sme_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sme_item_id')->constrained();
            $table->string('sme_item_size');
            $table->integer('sme_item_quantity');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['sme_item_id', 'sme_item_size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sme_item_variants');
    }
};

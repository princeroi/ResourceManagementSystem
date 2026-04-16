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
        Schema::create('sme_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('site_id')->constrained();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('po_number')->unique()->nullable();
            $table->string('po_file_path')->nullable();
            $table->date('po_date')->nullable();
            $table->string('dr_number')->nullable();
            $table->string('dr_file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sme_purchase_orders');
    }
};

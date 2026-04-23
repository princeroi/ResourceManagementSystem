<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_dr', function (Blueprint $table) {
            $table->id();

            // ── Polymorphic: billable = SmeBilling | UniformIssuanceBilling ──
            $table->morphs('billable'); // creates billable_id + billable_type

            // ── Polymorphic: sourceable = SmePurchaseOrder | UniformIssuances ──
            $table->morphs('sourceable'); // creates sourceable_id + sourceable_type

            // ── Shared columns ──
            $table->string('employee_name')->nullable(); // null = PO-level (SME)
            $table->string('dr_number');
            $table->date('date_signed')->nullable();
            $table->string('dr_image')->nullable();
            $table->string('remarks')->nullable();
            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_drs');
    }
};
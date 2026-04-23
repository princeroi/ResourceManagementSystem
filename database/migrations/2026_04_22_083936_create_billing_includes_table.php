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
        Schema::create('billing_includes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_id')->constrained('billings')->cascadeOnDelete();
            $table->nullableMorphs('includeable'); // includeable_type + includeable_id
            $table->decimal('amount', 12, 2);
            $table->string('label')->nullable(); // e.g. "SME - Client Name" or "Uniform - Client Name"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_includes');
    }
};

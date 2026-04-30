<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_supply_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_supply_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');           // completed, rejected, created, edited
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->json('note')->nullable();   // per-item snapshot array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_supply_request_logs');
    }
};
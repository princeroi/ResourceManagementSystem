<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_supply_categories', function (Blueprint $table) {
            $table->id();
            $table->string('office_supply_category_name');
            $table->timestamps();
        });

        Schema::create('office_supply_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_supply_category_id')
                ->constrained('office_supply_categories')
                ->cascadeOnDelete();
            $table->string('office_supply_name');
            $table->text('office_supply_description')->nullable();
            $table->decimal('office_supply_price', 10, 2)->nullable();
            $table->string('office_supply_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('office_supply_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_supply_item_id')
                ->constrained('office_supply_items')
                ->cascadeOnDelete();
            $table->string('office_supply_variant');
            $table->unsignedInteger('office_supply_quantity')->default(0);
            $table->timestamps();
        });

        Schema::create('office_supply_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requested_by');
            $table->date('request_date')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });

        Schema::create('office_supply_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_supply_request_id')
                ->constrained('office_supply_requests')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('office_supply_items')
                ->cascadeOnDelete();
            $table->foreignId('item_variant_id')
                ->constrained('office_supply_item_variants')
                ->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_supply_request_items');
        Schema::dropIfExists('office_supply_requests');
        Schema::dropIfExists('office_supply_item_variants');
        Schema::dropIfExists('office_supply_items');
        Schema::dropIfExists('office_supply_categories');
    }
};
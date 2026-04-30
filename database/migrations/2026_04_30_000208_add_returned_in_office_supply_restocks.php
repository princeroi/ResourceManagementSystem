<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_supply_restocks', function (Blueprint $table) {
            $table->date('returned_at')->nullable()->after('cancelled_at');
        });

        DB::statement("ALTER TABLE office_supply_restocks MODIFY COLUMN status ENUM('pending','partial','delivered','cancelled','returned') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('office_supply_restocks', function (Blueprint $table) {
            $table->dropColumn('returned_at');
        });

        DB::statement("ALTER TABLE office_supply_restocks MODIFY COLUMN status ENUM('pending','partial','delivered','cancelled') DEFAULT 'pending'");
    }
};
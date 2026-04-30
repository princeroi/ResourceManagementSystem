<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `office_supply_requests` MODIFY `status` ENUM('pending', 'completed', 'rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `office_supply_requests` MODIFY `status` ENUM('pending', 'completed') NOT NULL DEFAULT 'pending'");
    }
};
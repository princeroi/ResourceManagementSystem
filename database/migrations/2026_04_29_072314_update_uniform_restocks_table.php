<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uniform_restocks', function (Blueprint $table) {
            // Drop column
            $table->dropColumn('ordered_at');

            // Modify notes to nullable
            $table->string('notes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('uniform_restocks', function (Blueprint $table) {
            // Restore ordered_at
            $table->timestamp('ordered_at')->nullable();

            // Revert notes (not nullable)
            $table->string('notes')->nullable(false)->change();
        });
    }
};
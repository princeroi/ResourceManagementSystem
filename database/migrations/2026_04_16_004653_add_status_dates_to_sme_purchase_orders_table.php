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
        Schema::table('sme_purchase_orders', function (Blueprint $table) {
            $table->timestamp('pending_at')->nullable()->after('po_date');
            $table->timestamp('approved_at')->nullable()->after('pending_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('sme_purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['pending_at', 'approved_at', 'rejected_at']);
        });
    }

};

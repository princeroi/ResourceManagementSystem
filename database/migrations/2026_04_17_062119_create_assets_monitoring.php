<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Asset Categories ───────────────────────────────────────────────
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ── Assets (Core Inventory) ────────────────────────────────────────
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_category_id')->constrained('asset_categories')->cascadeOnDelete();

            // Identity
            $table->string('property_tag')->unique();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('specifications')->nullable();

            // Acquisition
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_cost', 12, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->string('purchase_order_number')->nullable();     // ✅ Added

            // Warranty
            $table->date('warranty_expiry_date')->nullable();        // ✅ Added

            // Depreciation
            $table->integer('useful_life_years')->nullable();
            $table->decimal('salvage_value', 12, 2)->nullable();

            // Current State
            $table->string('location')->nullable();
            $table->enum('condition', [
                'new', 'good', 'fair', 'poor', 'for_repair', 'condemned',
            ])->default('new');
            $table->enum('status', [
                'available', 'assigned', 'under_maintenance', 'disposed',
            ])->default('available');
            $table->enum('lifecycle_stage', [               // ✅ Added
                'active', 'end_of_life', 'disposed',
            ])->default('active');

            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Asset Assignments (Custodian Tracking) ─────────────────────────
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // ✅ Linked to users
            $table->string('assigned_to');
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->date('assigned_date');
            $table->date('returned_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // ── Asset Transfer History ─────────────────────────────────────────
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('transferred_from');
            $table->string('transferred_to');
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->date('transfer_date');
            $table->string('transferred_by');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // ── Maintenance / Repair History ───────────────────────────────────
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->enum('type', ['preventive', 'corrective', 'repair', 'upgrade']);
            $table->date('maintenance_date');
            $table->date('completed_date')->nullable();
            $table->string('performed_by')->nullable();
            $table->text('description');
            $table->decimal('cost', 12, 2)->nullable();
            $table->enum('status', [
                'scheduled', 'in_progress', 'completed', 'cancelled',
            ])->default('scheduled');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // ── Disposal / End-of-Life Tracking ────────────────────────────────
        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->enum('disposal_type', ['sold', 'donated', 'destroyed', 'returned_to_vendor']);
            $table->date('disposal_date');
            $table->string('disposed_by');
            $table->string('recipient')->nullable();        // buyer or recipient org
            $table->decimal('disposal_value', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // ── Audit / Inspection Log ─────────────────────────────────────────
        Schema::create('asset_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('audited_by');
            $table->date('audit_date');
            $table->enum('condition_found', [
                'new', 'good', 'fair', 'poor', 'for_repair', 'condemned', 'missing',
            ]);
            $table->string('location_found')->nullable();
            $table->boolean('matches_records')->default(true); // does physical match system records?
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // ── Incident / Issue Logs ──────────────────────────────────────────
        Schema::create('asset_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('reported_by');
            $table->date('reported_date');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->enum('status', ['open', 'resolved', 'escalated'])->default('open');
            $table->date('resolved_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });

        // ── Software / Subscriptions ───────────────────────────────────────
        Schema::create('software_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // e.g. Microsoft 365, Adobe CC
            $table->string('vendor')->nullable();
            $table->string('category')->nullable();         // e.g. Productivity, Security, Design
            $table->enum('license_type', [
                'perpetual',
                'subscription_monthly',
                'subscription_annual',
                'open_source',
                'freeware',
            ])->default('subscription_annual');
            $table->enum('plan_type', [
                'individual',
                'group',                                    // shared among a defined group
                'family',                                   // personal family plan
                'enterprise',
            ])->default('individual');

            // Seat / User Limits
            $table->integer('total_seats')->nullable();     // null = unlimited
            $table->integer('used_seats')->default(0);

            // Financial
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('billing_cycle')->nullable();    // monthly, annual, one-time
            $table->string('currency', 10)->default('PHP');

            // Dates
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('auto_renew')->default(false);

            // Credentials / Access
            $table->string('license_key')->nullable();
            $table->string('account_email')->nullable();    // account tied to the subscription
            $table->string('portal_url')->nullable();       // vendor management portal

            // Admin
            $table->string('managed_by')->nullable();       // person responsible
            $table->enum('status', [
                'active', 'expired', 'cancelled', 'pending',
            ])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Software Subscription Members ─────────────────────────────────
        // Tracks who is included in group/family/enterprise plans
        Schema::create('subscription_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_subscription_id')
                ->constrained('software_subscriptions')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('member_name');                  // fallback if no user account
            $table->string('member_email')->nullable();
            $table->string('department')->nullable();
            $table->enum('role', [
                'admin',                                    // manages the plan
                'member',                                   // regular user
            ])->default('member');
            $table->date('added_date');
            $table->date('removed_date')->nullable();       // null = currently active member
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // ── Software ↔ Asset Link (Install tracking) ───────────────────────
        // Track which software is installed on which physical asset
        Schema::create('asset_software', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('software_subscription_id')
                ->constrained('software_subscriptions')
                ->cascadeOnDelete();
            $table->date('installed_date')->nullable();
            $table->date('uninstalled_date')->nullable();   // null = currently installed
            $table->string('installed_by')->nullable();
            $table->string('version')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_software');
        Schema::dropIfExists('subscription_members');
        Schema::dropIfExists('software_subscriptions');
        Schema::dropIfExists('asset_incidents');
        Schema::dropIfExists('asset_audits');
        Schema::dropIfExists('asset_disposals');
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('asset_transfers');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};
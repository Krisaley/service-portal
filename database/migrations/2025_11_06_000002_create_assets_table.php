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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('asset_type'); // equipment, license, rams, insurance, client_asset
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'retired'])->default('active');

            // Maintenance scheduling
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->string('service_frequency')->nullable(); // daily, weekly, monthly, quarterly, annually, custom
            $table->integer('service_frequency_days')->nullable(); // for custom frequency

            // Expiry tracking
            $table->date('expiry_date')->nullable();
            $table->integer('expiry_warning_days')->default(30); // days before expiry to warn

            // Purchase info
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->string('warranty_expiry')->nullable();

            $table->json('custom_fields')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'asset_type']);
            $table->index(['team_id', 'customer_id']);
            $table->index('next_service_date');
            $table->index('expiry_date');
        });

        // Asset maintenance history
        Schema::create('asset_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('maintenance_date');
            $table->string('type'); // service, repair, inspection, calibration
            $table->text('description');
            $table->text('notes')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('parts_used')->nullable();
            $table->date('next_due_date')->nullable();
            $table->timestamps();

            $table->index('asset_id');
            $table->index('maintenance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance_logs');
        Schema::dropIfExists('assets');
    }
};

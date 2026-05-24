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
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('employee_id')->nullable();
            $table->string('vehicle_type', 50)->nullable();
            $table->string('vehicle_number', 50)->nullable();
            $table->string('license_number', 50)->nullable();
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->decimal('per_delivery_rate', 10, 2)->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->integer('total_deliveries')->default(0);
            $table->integer('successful_deliveries')->default(0);
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->enum('availability_status', ['available', 'on_duty', 'off_duty', 'on_leave'])->default('off_duty');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('documents')->nullable();
            $table->json('working_hours')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'availability_status', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riders');
    }
};

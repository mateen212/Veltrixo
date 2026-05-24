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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('route_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('invoice_id')->nullable(); // FK added after invoices table via separate migration
            $table->date('delivery_date');
            $table->time('scheduled_time')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->enum('status', ['scheduled','assigned','in_progress','delivered','missed','cancelled','rescheduled','pending'])->default('scheduled');
            $table->string('delivery_otp', 6)->nullable();
            $table->string('qr_code_token')->nullable()->unique();
            $table->boolean('otp_verified')->default(false);
            $table->string('proof_image')->nullable();
            $table->decimal('delivery_latitude', 10, 8)->nullable();
            $table->decimal('delivery_longitude', 11, 8)->nullable();
            $table->text('rider_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->string('missed_reason')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['tenant_id', 'delivery_date', 'status']);
            $table->index(['rider_id', 'delivery_date', 'status']);
            $table->index(['user_id', 'delivery_date']);
            $table->index(['subscription_id', 'delivery_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

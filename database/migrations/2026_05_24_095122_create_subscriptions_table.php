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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly', 'custom'])->default('daily');
            $table->json('delivery_days')->nullable();
            $table->time('preferred_delivery_time')->nullable();
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->date('next_delivery_date')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired', 'pending'])->default('pending');
            $table->timestamp('paused_at')->nullable();
            $table->date('pause_until')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'user_id', 'status']);
            $table->index('next_delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

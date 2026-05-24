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
        Schema::create('subscription_skips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('skip_date');
            $table->string('reason')->nullable();
            $table->enum('type', ['customer_skip', 'admin_skip', 'auto_skip'])->default('customer_skip');
            $table->timestamps();
            $table->unique(['subscription_id', 'skip_date']);
            $table->index(['subscription_id', 'skip_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_skips');
    }
};

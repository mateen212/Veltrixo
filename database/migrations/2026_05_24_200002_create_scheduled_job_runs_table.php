<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scheduled_job_runs', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key')->unique()->comment('e.g. generate_deliveries:1:2026-05-24');
            $table->string('job_class');
            $table->string('status')->default('pending'); // pending|running|completed|failed
            $table->json('payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['job_class', 'status']);
            $table->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('scheduled_job_runs'); }
};

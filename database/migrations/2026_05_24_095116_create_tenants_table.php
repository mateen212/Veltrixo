<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->json('branding')->nullable();
            $table->json('address')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('currency_symbol', 5)->default('$');
            $table->string('timezone')->default('UTC');
            $table->string('locale', 10)->default('en');
            $table->enum('status', ['active', 'inactive', 'suspended', 'trial'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('features')->nullable();
            $table->json('limits')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tenants'); }
};

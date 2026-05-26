<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('meta');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedSmallInteger('delivery_radius_km')->default(5)->after('longitude');
            $table->enum('verification_status', [
                'pending_verification', 'approved', 'rejected', 'suspended',
            ])->default('pending_verification')->after('delivery_radius_km');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            $table->text('rejection_reason')->nullable()->after('verified_by');
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('account_status', ['pending', 'active', 'suspended'])->default('active')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'latitude', 'longitude', 'delivery_radius_km',
                'verification_status', 'verified_at', 'verified_by', 'rejection_reason',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_status');
        });
    }
};

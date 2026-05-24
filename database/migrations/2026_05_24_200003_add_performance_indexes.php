<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->index(['tenant_id', 'delivery_date'], 'idx_deliveries_tenant_date');
            $table->index(['status', 'delivery_date'],    'idx_deliveries_status_date');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index(['tenant_id', 'created_at'], 'idx_wallet_tx_tenant_created');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['next_delivery_date', 'status'], 'idx_subscriptions_next_status');
        });
    }
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex('idx_deliveries_tenant_date');
            $table->dropIndex('idx_deliveries_status_date');
        });
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_wallet_tx_tenant_created');
        });
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_subscriptions_next_status');
        });
    }
};

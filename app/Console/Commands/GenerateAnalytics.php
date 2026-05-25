<?php

namespace App\Console\Commands;

use App\Jobs\Report\GenerateAnalyticsReportJob;
use App\Models\Tenant;
use Illuminate\Console\Command;

class GenerateAnalytics extends Command
{
    protected $signature = 'analytics:generate-daily {--date= : Date (YYYY-MM-DD, default: yesterday)} {--tenant= : Specific tenant ID}';
    protected $description = 'Aggregate daily KPI metrics for all active tenants';

    public function handle(): int
    {
        $date     = $this->option('date') ?? today()->subDay()->toDateString();
        $tenantId = $this->option('tenant');

        $this->info("Generating analytics for {$date}...");

        $query = Tenant::where('status', 'active');

        if ($tenantId) {
            $query->where('id', $tenantId);
        }

        $tenants = $query->get();

        foreach ($tenants as $tenant) {
            GenerateAnalyticsReportJob::dispatch($tenant->id, $date)
                ->onQueue('reports');
            $this->line("  Queued analytics for tenant: {$tenant->name} (#{$tenant->id})");
        }

        $this->info("Done. {$tenants->count()} tenant(s) queued for analytics.");

        return self::SUCCESS;
    }
}

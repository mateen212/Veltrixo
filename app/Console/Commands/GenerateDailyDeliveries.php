<?php

namespace App\Console\Commands;

use App\Jobs\Delivery\GenerateDeliveriesJob;
use App\Models\Tenant;
use Illuminate\Console\Command;

class GenerateDailyDeliveries extends Command
{
    protected $signature = 'deliveries:generate {--date= : Date in YYYY-MM-DD format} {--tenant= : Specific tenant ID}';
    protected $description = 'Generate deliveries for all active tenants for the given date (default: today)';

    public function handle(): int
    {
        $date = $this->option('date') ?? today()->toDateString();
        $tenantId = $this->option('tenant');

        $this->info("Generating deliveries for {$date}...");

        $query = Tenant::where('status', 'active');

        if ($tenantId) {
            $query->where('id', $tenantId);
        }

        $tenants = $query->get();

        foreach ($tenants as $tenant) {
            GenerateDeliveriesJob::dispatch($tenant->id, $date)
                ->onQueue('deliveries');
            $this->line("  Queued for tenant: {$tenant->name} (#{$tenant->id})");
        }

        $this->info("Done. {$tenants->count()} tenant(s) queued.");

        return self::SUCCESS;
    }
}

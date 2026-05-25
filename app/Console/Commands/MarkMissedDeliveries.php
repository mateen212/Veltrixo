<?php

namespace App\Console\Commands;

use App\Models\Delivery;
use App\Models\Tenant;
use App\Events\Delivery\DeliveryMissed;
use Illuminate\Console\Command;

class MarkMissedDeliveries extends Command
{
    protected $signature = 'deliveries:mark-missed {--date= : Date to check (default: today)} {--tenant= : Specific tenant ID}';
    protected $description = 'Mark deliveries that are still pending/assigned past their expected window as missed';

    public function handle(): int
    {
        $date     = $this->option('date') ?? today()->toDateString();
        $tenantId = $this->option('tenant');

        $this->info("Checking for missed deliveries on {$date}...");

        $query = Delivery::where('delivery_date', $date)
            ->whereIn('status', ['scheduled', 'pending', 'assigned'])
            ->with(['user', 'rider']);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $missed = $query->get();
        $count  = 0;

        foreach ($missed as $delivery) {
            $delivery->update([
                'status'        => 'missed',
                'missed_reason' => 'auto_detected',
            ]);

            event(new DeliveryMissed($delivery));
            $count++;
        }

        $this->info("Done. {$count} delivery/deliveries marked as missed.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Observers;

use App\Jobs\Invoice\GenerateInvoiceJob;
use App\Jobs\Notification\SendDeliveryReceiptJob;
use App\Models\Delivery;

class DeliveryObserver
{
    /**
     * Fire invoice + receipt notification after a delivery is marked delivered.
     */
    public function updated(Delivery $delivery): void
    {
        if ($delivery->isDirty('status') && $delivery->status === 'delivered') {
            GenerateInvoiceJob::dispatch($delivery->id)
                ->onQueue('invoices')
                ->delay(now()->addSeconds(5));

            SendDeliveryReceiptJob::dispatch($delivery->id)
                ->onQueue('notifications')
                ->delay(now()->addSeconds(10));
        }
    }

    /**
     * Set tenant_id automatically on creation if missing.
     */
    public function creating(Delivery $delivery): void
    {
        if (empty($delivery->tenant_id) && $user = auth()->user()) {
            $delivery->tenant_id = $user->tenant_id;
        }
    }
}

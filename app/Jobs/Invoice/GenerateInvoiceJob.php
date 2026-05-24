<?php

namespace App\Jobs\Invoice;

use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $subscriptionId,
        public readonly string $period, // YYYY-MM
    ) {}

    public function handle(): void
    {
        $subscription = Subscription::with('activeItems.product', 'user', 'tenant')->find($this->subscriptionId);

        if (!$subscription) return;

        $month = Carbon::parse($this->period . '-01');

        // Avoid duplicate invoices
        $exists = Invoice::where('subscription_id', $subscription->id)
            ->whereYear('issue_date', $month->year)
            ->whereMonth('issue_date', $month->month)
            ->exists();

        if ($exists) return;

        $subtotal = $subscription->activeItems->sum(fn ($item) => $item->quantity * $item->unit_price);

        $invoice = Invoice::create([
            'uuid'             => Str::uuid(),
            'tenant_id'        => $subscription->tenant_id,
            'user_id'          => $subscription->user_id,
            'subscription_id'  => $subscription->id,
            'invoice_number'   => $this->generateInvoiceNumber($subscription->tenant_id),
            'subtotal'         => $subtotal,
            'tax_amount'       => 0,
            'discount_amount'  => 0,
            'total'            => $subtotal,
            'paid_amount'      => 0,
            'due_amount'       => $subtotal,
            'status'           => 'draft',
            'issue_date'       => $month,
            'due_date'         => $month->copy()->endOfMonth(),
        ]);

        foreach ($subscription->activeItems as $item) {
            $invoice->items()->create([
                'description' => $item->product->name,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
                'subtotal'    => $item->quantity * $item->unit_price,
                'tax_rate'    => 0,
                'tax_amount'  => 0,
            ]);
        }

        $invoice->update(['status' => 'sent']);
    }

    private function generateInvoiceNumber(int $tenantId): string
    {
        $count = Invoice::where('tenant_id', $tenantId)->count() + 1;
        return 'INV-' . $tenantId . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function tags(): array
    {
        return ['invoices', "subscription:{$this->subscriptionId}"];
    }
}

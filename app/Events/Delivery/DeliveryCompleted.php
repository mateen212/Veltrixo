<?php

namespace App\Events\Delivery;

use App\Models\Delivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Delivery $delivery) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("customer.{$this->delivery->user_id}"),
            new Channel("tenant.{$this->delivery->tenant_id}.deliveries"),
        ];
    }

    public function broadcastAs(): string { return 'delivery.completed'; }
}

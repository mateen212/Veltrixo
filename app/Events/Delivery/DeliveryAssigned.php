<?php

namespace App\Events\Delivery;

use App\Models\Delivery;
use App\Models\Rider;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Delivery $delivery,
        public readonly Rider $rider,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("rider.{$this->rider->id}"),
            new Channel("tenant.{$this->delivery->tenant_id}.deliveries"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.assigned';
    }
}

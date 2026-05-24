<?php

namespace App\DTOs\Subscription;

class CreateSubscriptionDTO
{
    public function __construct(
        public readonly int $tenantId,
        public readonly int $userId,
        public readonly ?int $addressId,
        public readonly string $frequency,
        public readonly ?array $deliveryDays,
        public readonly string $startsAt,
        public readonly ?string $endsAt = null,
        public readonly ?string $preferredDeliveryTime = null,
        public readonly array $items = [],
        public readonly ?string $notes = null,
    ) {}

    public static function fromArray(array $data, int $tenantId, int $userId): self
    {
        return new self(
            tenantId: $tenantId,
            userId: $userId,
            addressId: $data['address_id'] ?? null,
            frequency: $data['frequency'],
            deliveryDays: $data['delivery_days'] ?? null,
            startsAt: $data['starts_at'],
            endsAt: $data['ends_at'] ?? null,
            preferredDeliveryTime: $data['preferred_delivery_time'] ?? null,
            items: $data['items'] ?? [],
            notes: $data['notes'] ?? null,
        );
    }
}

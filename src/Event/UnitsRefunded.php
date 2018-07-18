<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

use Prooph\Common\Messaging\DomainEvent;
use Prooph\Common\Messaging\PayloadTrait;

final class UnitsRefunded extends DomainEvent
{
    use PayloadTrait;

    public function __construct(string $orderNumber, iterable $unitIds, iterable $shipmentIds, int $amount)
    {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'unit_ids' => $unitIds,
            'shipment_ids' => $shipmentIds,
            'amount' => $amount,
        ]);
    }

    public function orderNumber(): string
    {
        return $this->payload['order_number'];
    }

    public function unitIds(): iterable
    {
        return $this->payload['unit_ids'];
    }

    public function shipmentIds(): iterable
    {
        return $this->payload['shipment_ids'];
    }

    public function amount(): int
    {
        return $this->payload['amount'];
    }
}

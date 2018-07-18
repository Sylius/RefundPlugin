<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;

final class RefundUnits extends Command
{
    use PayloadTrait;

    public function __construct(string $orderNumber, array $refundedUnitIds, array $refundedShipmentIds)
    {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'refunded_unit_ids' => $refundedUnitIds,
            'refunded_shipment_ids' => $refundedShipmentIds,
        ]);
    }

    public function orderNumber(): string
    {
        return $this->payload()['order_number'];
    }

    public function refundedUnitIds(): array
    {
        return $this->payload()['refunded_unit_ids'];
    }

    public function refundedShipmentIds(): array
    {
        return $this->payload()['refunded_shipment_ids'];
    }
}

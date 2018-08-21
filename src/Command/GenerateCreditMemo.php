<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;

final class GenerateCreditMemo extends Command
{
    use PayloadTrait;

    public function __construct(string $orderNumber, int $total, array $unitIds, array $shipmentIds, string $comment)
    {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'total' => $total,
            'unit_ids' => $unitIds,
            'shipment_ids' => $shipmentIds,
            'comment' => $comment,
        ]);
    }

    public function orderNumber(): string
    {
        return $this->payload['order_number'];
    }

    public function total(): int
    {
        return $this->payload['total'];
    }

    public function unitIds(): array
    {
        return $this->payload['unit_ids'];
    }

    public function shipmentIds(): array
    {
        return $this->payload['shipment_ids'];
    }

    public function comment(): string
    {
        return $this->payload['comment'];
    }
}

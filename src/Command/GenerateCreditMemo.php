<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;

final class GenerateCreditMemo extends Command
{
    use PayloadTrait;

    public function __construct(string $orderNumber, int $total, array $unitIds, array $shipments, string $comment)
    {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'total' => $total,
            'units' => $unitIds,
            'shipments' => $shipments,
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

    public function units(): array
    {
        return $this->payload['units'];
    }

    public function shipments(): array
    {
        return $this->payload['shipments'];
    }

    public function comment(): string
    {
        return $this->payload['comment'];
    }
}

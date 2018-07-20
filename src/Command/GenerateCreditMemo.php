<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;

final class GenerateCreditMemo extends Command
{
    use PayloadTrait;

    public function __construct(string $orderNumber, int $total)
    {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'total' => $total,
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
}

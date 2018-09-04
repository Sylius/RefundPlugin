<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;

final class RefundUnits extends Command
{
    use PayloadTrait;

    public function __construct(
        string $orderNumber,
        array $units,
        array $shipments,
        int $paymentMethodId,
        string $comment
    ) {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'units' => $units,
            'shipments' => $shipments,
            'payment_method_id' => $paymentMethodId,
            'comment' => $comment,
        ]);
    }

    public function orderNumber(): string
    {
        return $this->payload()['order_number'];
    }

    public function units(): array
    {
        return $this->payload()['units'];
    }

    public function shipments(): array
    {
        return $this->payload()['shipments'];
    }

    public function paymentMethodId(): int
    {
        return $this->payload()['payment_method_id'];
    }

    public function comment(): string
    {
        return $this->payload()['comment'];
    }
}

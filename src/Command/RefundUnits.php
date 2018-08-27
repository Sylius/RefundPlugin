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
        array $shipmentIds,
        int $paymentMethodId,
        string $comment
    ) {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'units' => $units,
            'shipment_ids' => $shipmentIds,
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

    public function shipmentIds(): array
    {
        return $this->payload()['shipment_ids'];
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

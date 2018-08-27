<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

use Prooph\Common\Messaging\DomainEvent;
use Prooph\Common\Messaging\PayloadTrait;

final class UnitsRefunded extends DomainEvent
{
    use PayloadTrait;

    public function __construct(
        string $orderNumber,
        array $units,
        array $shipmentIds,
        int $paymentMethodId,
        int $amount,
        string $currencyCode,
        string $comment
    ) {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'units' => $units,
            'shipment_ids' => $shipmentIds,
            'amount' => $amount,
            'currency_code' => $currencyCode,
            'payment_method_id' => $paymentMethodId,
            'comment' => $comment,
        ]);
    }

    public function orderNumber(): string
    {
        return $this->payload['order_number'];
    }

    public function units(): array
    {
        return $this->payload['units'];
    }

    public function shipmentIds(): array
    {
        return $this->payload['shipment_ids'];
    }

    public function amount(): int
    {
        return $this->payload['amount'];
    }

    public function paymentMethodId(): int
    {
        return $this->payload['payment_method_id'];
    }

    public function currencyCode(): string
    {
        return $this->payload['currency_code'];
    }

    public function comment(): string
    {
        return $this->payload['comment'];
    }
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

use Prooph\Common\Messaging\DomainEvent;
use Prooph\Common\Messaging\PayloadTrait;

final class UnitsRefunded extends DomainEvent
{
    use PayloadTrait;

    /** @var string */
    private $orderNumber;

    /** @var iterable */
    private $unitIds;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, iterable $unitIds, int $amount)
    {
        $this->init();
        $this->setPayload([
            'order_number' => $orderNumber,
            'unit_ids' => $unitIds,
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

    public function amount(): int
    {
        return $this->payload['amount'];
    }
}

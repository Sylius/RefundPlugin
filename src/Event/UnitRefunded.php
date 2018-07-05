<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class UnitRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var int */
    private $unitId;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, int $unitId, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->unitId = $unitId;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function unitId(): int
    {
        return $this->unitId;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}

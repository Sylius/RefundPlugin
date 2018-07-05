<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class UnitsRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var iterable|int[] */
    private $unitIds;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, iterable $unitIds, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->unitIds = $unitIds;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function unitIds(): iterable
    {
        return $this->unitIds;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}

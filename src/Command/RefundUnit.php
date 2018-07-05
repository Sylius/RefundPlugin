<?php

namespace Sylius\RefundPlugin\Command;

final class RefundUnit
{
    /** @var string */
    private $orderNumber;

    /** @var array|int[] */
    private $refundedUnitId;

    public function __construct(string $orderNumber, array $refundedUnitId)
    {
        $this->orderNumber = $orderNumber;
        $this->refundedUnitId = $refundedUnitId;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function refundedUnitIds(): array
    {
        return $this->refundedUnitId;
    }
}

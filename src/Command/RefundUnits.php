<?php

namespace Sylius\RefundPlugin\Command;

final class RefundUnits
{
    /** @var string */
    private $orderNumber;

    /** @var array|int[] */
    private $refundedUnitIds;

    public function __construct(string $orderNumber, array $refundedUnitId)
    {
        $this->orderNumber = $orderNumber;
        $this->refundedUnitIds = $refundedUnitId;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function refundedUnitIds(): array
    {
        return $this->refundedUnitIds;
    }
}

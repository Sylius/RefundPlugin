<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

interface RefunderInterface
{
    /**
     * @return int refunded units total
     */
    public function refundFromOrder(array $units, string $orderNumber): int;
}

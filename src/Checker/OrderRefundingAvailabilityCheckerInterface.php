<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

interface OrderRefundingAvailabilityCheckerInterface
{
    public function __invoke(string $orderNumber): bool;
}

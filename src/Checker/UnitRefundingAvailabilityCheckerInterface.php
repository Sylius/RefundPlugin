<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\RefundPlugin\Model\RefundType;

interface UnitRefundingAvailabilityCheckerInterface
{
    public function __invoke(int $unitId, RefundType $refundType): bool;
}

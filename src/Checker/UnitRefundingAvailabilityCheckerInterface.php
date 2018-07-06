<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

interface UnitRefundingAvailabilityCheckerInterface
{
    public function __invoke(int $unitId): bool;
}

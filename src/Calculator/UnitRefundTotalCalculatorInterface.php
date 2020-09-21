<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Calculator;

use Sylius\RefundPlugin\Model\RefundType;

interface UnitRefundTotalCalculatorInterface
{
    public function calculateForUnitWithIdAndType(int $id, RefundType $refundType, ?float $amount = null): int;
}

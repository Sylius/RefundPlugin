<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Calculator;

use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class UnitRefundTotalCalculator implements UnitRefundTotalCalculatorInterface
{
    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    public function __construct(RemainingTotalProviderInterface $remainingTotalProvider)
    {
        $this->remainingTotalProvider = $remainingTotalProvider;
    }

    public function calculateForUnitWithIdAndType(int $id, RefundType $refundType, ?float $amount = null): int
    {
        if ($amount !== null) {
            return (int) round($amount * 100);
        }

        return $this->remainingTotalProvider->getTotalLeftToRefund($id, $refundType);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class UnitRefundingAvailabilityChecker implements UnitRefundingAvailabilityCheckerInterface
{
    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    public function __construct(
        RemainingTotalProviderInterface $remainingTotalProvider
    ) {
        $this->remainingTotalProvider = $remainingTotalProvider;
    }

    public function __invoke(int $unitId, RefundType $refundType): bool
    {
        return $this->remainingTotalProvider->getTotalLeftToRefund($unitId, $refundType) > 0;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Twig;

use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;
use Sylius\RefundPlugin\Provider\UnitRefundedTotalProviderInterface;

final class OrderRefundsExtension extends \Twig_Extension
{
    /** @var OrderRefundedTotalProviderInterface */
    private $orderRefundedTotalProvider;

    /** @var UnitRefundedTotalProviderInterface */
    private $unitRefundedTotalProvider;

    /** @var UnitRefundingAvailabilityCheckerInterface */
    private $unitRefundingAvailabilityChecker;

    public function __construct(
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider,
        UnitRefundedTotalProviderInterface $unitRefundedTotalProvider,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker
    ) {
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
        $this->unitRefundedTotalProvider = $unitRefundedTotalProvider;
        $this->unitRefundingAvailabilityChecker = $unitRefundingAvailabilityChecker;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function(
                'order_refunded_total',
                [$this->orderRefundedTotalProvider, '__invoke']
            ),
            new \Twig_Function(
                'unit_refunded_total',
                [$this, 'getUnitRefundedTotal']
            ),
            new \Twig_Function(
                'can_unit_be_refunded',
                [$this, 'canUnitBeRefunded']
            ),
            new \Twig_Function(
                'unit_refund_left',
                [$this, 'getUnitRefundLeft']
            ),
        ];
    }

    public function canUnitBeRefunded(int $unitId, string $refundType): bool
    {
        return $this->unitRefundingAvailabilityChecker->__invoke($unitId, new RefundType($refundType));
    }

    public function getUnitRefundedTotal(int $unitId, string $refundType): int
    {
        return $this->unitRefundedTotalProvider->__invoke($unitId, new RefundType($refundType));
    }

    public function getUnitRefundLeft(int $unitId, string $refundType, int $unitTotal): float
    {
        return ($unitTotal - $this->getUnitRefundedTotal($unitId, $refundType)) / 100;
    }
}

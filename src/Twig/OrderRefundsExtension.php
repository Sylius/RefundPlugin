<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Twig;

use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderRefundsExtension extends \Twig_Extension
{
    /** @var OrderRefundedTotalProviderInterface */
    private $orderRefundedTotalProvider;

    /** @var UnitRefundingAvailabilityCheckerInterface */
    private $unitRefundingAvailabilityChecker;

    public function __construct(
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker
    ) {
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
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
                'can_unit_be_refunded',
                [$this, 'canUnitBeRefunded']
            ),
        ];
    }

    public function canUnitBeRefunded(int $unitId, string $refundType): bool
    {
        return $this->unitRefundingAvailabilityChecker->__invoke($unitId, new RefundType($refundType));
    }
}

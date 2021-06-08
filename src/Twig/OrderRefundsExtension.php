<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Twig;

use spec\Sylius\Component\User\Security\Generator\UniquePinGeneratorSpec;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
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

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider,
        UnitRefundedTotalProviderInterface $unitRefundedTotalProvider,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
        $this->unitRefundedTotalProvider = $unitRefundedTotalProvider;
        $this->unitRefundingAvailabilityChecker = $unitRefundingAvailabilityChecker;
        $this->orderRepository = $orderRepository;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function(
                'order_refunded_total',
                [$this, 'getRefundedTotal']
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

    public function getRefundedTotal(string $orderNumber): int
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        return ($this->orderRefundedTotalProvider)($order);
    }
}

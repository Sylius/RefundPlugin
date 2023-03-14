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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Factory\RefundTypeFactoryInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;
use Sylius\RefundPlugin\Provider\UnitRefundedTotalProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderRefundsExtension extends AbstractExtension
{
    private OrderRefundedTotalProviderInterface $orderRefundedTotalProvider;

    private UnitRefundedTotalProviderInterface $unitRefundedTotalProvider;

    private UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker;

    private OrderRepositoryInterface $orderRepository;

    private RepositoryInterface $refundPaymentRepository;

    private RefundTypeFactoryInterface $refundTypeFactory;

    public function __construct(
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider,
        UnitRefundedTotalProviderInterface $unitRefundedTotalProvider,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $refundPaymentRepository,
        RefundTypeFactoryInterface $refundTypeFactory,
    ) {
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
        $this->unitRefundedTotalProvider = $unitRefundedTotalProvider;
        $this->unitRefundingAvailabilityChecker = $unitRefundingAvailabilityChecker;
        $this->orderRepository = $orderRepository;
        $this->refundPaymentRepository = $refundPaymentRepository;
        $this->refundTypeFactory = $refundTypeFactory;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'order_refunded_total',
                [$this, 'getRefundedTotal'],
            ),
            new TwigFunction(
                'unit_refunded_total',
                [$this, 'getUnitRefundedTotal'],
            ),
            new TwigFunction(
                'can_unit_be_refunded',
                [$this, 'canUnitBeRefunded'],
            ),
            new TwigFunction(
                'unit_refund_left',
                [$this, 'getUnitRefundLeft'],
            ),
            new TwigFunction(
                'get_all_refund_payments_by_order',
                [$this, 'getAllRefundPaymentsByOrder'],
            ),
        ];
    }

    public function canUnitBeRefunded(int $unitId, string $refundType): bool
    {
        return $this->unitRefundingAvailabilityChecker->__invoke($unitId, $this->refundTypeFactory->createNew($refundType));
    }

    public function getUnitRefundedTotal(int $unitId, string $refundType): int
    {
        return $this->unitRefundedTotalProvider->__invoke($unitId, $this->refundTypeFactory->createNew($refundType));
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

    public function getAllRefundPaymentsByOrder(OrderInterface $order): array
    {
        return $this->refundPaymentRepository->findBy(['order' => $order]);
    }
}

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

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class RemainingTotalProviderSpec extends ObjectBehavior
{
    function let(ServiceProviderInterface $refundUnitTotalProviders, RepositoryInterface $refundRepository): void
    {
        $this->beConstructedWith($refundUnitTotalProviders, $refundRepository);
    }

    function it_implements_remaining_total_provider_interface(): void
    {
        $this->shouldImplement(RemainingTotalProviderInterface::class);
    }

    function it_returns_remaining_total_to_refund(
        ServiceProviderInterface $refundUnitTotalProviders,
        RefundUnitTotalProviderInterface $refundUnitTotalProvider,
        RepositoryInterface $refundRepository,
        RefundInterface $refund,
    ): void {
        $refundType = RefundType::orderItemUnit();

        $refundUnitTotalProviders->get($refundType->getValue())->willReturn($refundUnitTotalProvider);
        $refundUnitTotalProvider->getRefundUnitTotal(1)->willReturn(1000);

        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([$refund])
        ;

        $refund->getAmount()->willReturn(500);

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(500);
    }

    function it_returns_unit_total_if_there_is_no_refund_for_this_unit_yet(
        ServiceProviderInterface $refundUnitTotalProviders,
        RefundUnitTotalProviderInterface $refundUnitTotalProvider,
        RepositoryInterface $refundRepository,
    ): void {
        $refundType = RefundType::orderItemUnit();

        $refundUnitTotalProviders->get($refundType->getValue())->willReturn($refundUnitTotalProvider);
        $refundUnitTotalProvider->getRefundUnitTotal(1)->willReturn(1000);

        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([])
        ;

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(1000);
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_returns_unit_remaining_total_to_refund_with_deprecations(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
        OrderItemUnitInterface $orderItemUnit,
        RefundInterface $refund,
    ): void {
        $this->beConstructedWith($orderItemUnitRepository, $adjustmentRepository, $refundRepository);

        $refundType = RefundType::orderItemUnit();

        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([$refund])
        ;

        $refund->getAmount()->willReturn(500);
        $orderItemUnit->getTotal()->willReturn(1000);

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(500);
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_returns_unit_total_if_there_is_no_refund_for_this_unit_yet_with_deprecations(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
        OrderItemUnitInterface $orderItemUnit,
    ): void {
        $this->beConstructedWith($orderItemUnitRepository, $adjustmentRepository, $refundRepository);

        $refundType = RefundType::orderItemUnit();

        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([])
        ;

        $orderItemUnit->getTotal()->willReturn(1000);

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(1000);
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_returns_shipment_remaining_total_to_refund_with_deprecations(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
        AdjustmentInterface $shippingAdjustment,
        ShipmentInterface $shipment,
        RefundInterface $refund,
    ): void {
        $this->beConstructedWith($orderItemUnitRepository, $adjustmentRepository, $refundRepository);

        $refundType = RefundType::shipment();

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;

        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([$refund])
        ;

        $refund->getAmount()->willReturn(500);
        $shippingAdjustment->getShipment()->willReturn($shipment);
        $shipment->getAdjustmentsTotal()->willReturn(1000);

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(500);
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_returns_shipment_total_if_there_is_no_refund_for_this_unit_yet_with_deprecations(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
        AdjustmentInterface $shippingAdjustment,
        ShipmentInterface $shipment,
    ): void {
        $this->beConstructedWith($orderItemUnitRepository, $adjustmentRepository, $refundRepository);

        $refundType = RefundType::shipment();

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;

        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([])
        ;

        $shippingAdjustment->getShipment()->willReturn($shipment);
        $shipment->getAdjustmentsTotal()->willReturn(1000);

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(1000);
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_throws_exception_if_there_is_no_order_item_unit_with_given_id_with_deprecations(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
    ): void {
        $this->beConstructedWith($orderItemUnitRepository, $adjustmentRepository, $refundRepository);

        $orderItemUnitRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getTotalLeftToRefund', [1, RefundType::shipment()])
        ;
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_throws_exception_if_there_is_no_shipment_with_given_id(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
    ): void {
        $this->beConstructedWith($orderItemUnitRepository, $adjustmentRepository, $refundRepository);

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn(null)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getTotalLeftToRefund', [1, RefundType::shipment()])
        ;
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Entity\ShipmentInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RemainingTotalProviderSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository
    ): void {
        $this->beConstructedWith($orderItemUnitRepository, $adjustmentRepository, $refundRepository);
    }

    function it_implements_remaining_total_provider_interface(): void
    {
        $this->shouldImplement(RemainingTotalProviderInterface::class);
    }

    function it_returns_unit_remaining_total_to_refund(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $refundRepository,
        OrderItemUnitInterface $orderItemUnit,
        RefundInterface $refund
    ): void {
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

    function it_returns_unit_total_if_there_is_no_refund_for_this_unit_yet(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $refundRepository,
        OrderItemUnitInterface $orderItemUnit
    ): void {
        $refundType = RefundType::orderItemUnit();

        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([])
        ;

        $orderItemUnit->getTotal()->willReturn(1000);

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(1000);
    }

    function it_returns_shipment_remaining_total_to_refund(
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
        AdjustmentInterface $shippingAdjustment,
        ShipmentInterface $shipment,
        RefundInterface $refund
    ): void {
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

    function it_returns_shipment_total_if_there_is_no_refund_for_this_unit_yet(
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
        AdjustmentInterface $shippingAdjustment,
        ShipmentInterface $shipment
    ): void {
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

    function it_throws_exception_if_there_is_no_order_item_unit_with_given_id(
        RepositoryInterface $orderItemUnitRepository
    ): void {
        $orderItemUnitRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getTotalLeftToRefund', [1, RefundType::shipment()])
        ;
    }

    function it_throws_exception_if_there_is_no_shipment_with_given_id(
        RepositoryInterface $adjustmentRepository
    ): void {
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

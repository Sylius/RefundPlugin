<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class UnitRefundingAvailabilityCheckerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $refundRepository,
        RemainingTotalProviderInterface $remainingOrderItemUnitTotalProvider
    ): void {
        $this->beConstructedWith($refundRepository, $remainingOrderItemUnitTotalProvider);
    }

    function it_implements_unit_refunding_availability_checker_interface(): void
    {
        $this->shouldImplement(UnitRefundingAvailabilityCheckerInterface::class);
    }

    function it_return_false_it_there_is_already_shipment_refund_with_given_id(
        RepositoryInterface $refundRepository,
        RefundInterface $refund
    ): void {
        $refundRepository
            ->findOneBy(['refundedUnitId' => 1, 'type' => RefundType::shipment()->__toString()])
            ->willReturn($refund)
        ;

        $this->__invoke(1, RefundType::shipment())->shouldReturn(false);
    }

    function it_return_true_it_there_is_no_shipment_refund_with_given_id(RepositoryInterface $refundRepository): void
    {
        $refundRepository
            ->findOneBy(['refundedUnitId' => 1, 'type' => RefundType::shipment()->__toString()])
            ->willReturn(null)
        ;

        $this->__invoke(1, RefundType::shipment())->shouldReturn(true);
    }

    function it_returns_false_if_remaining_order_item_unit_total_is_0(
        RemainingTotalProviderInterface $remainingOrderItemUnitTotalProvider
    ): void {
        $remainingOrderItemUnitTotalProvider->getTotalLeftToRefund(1)->willReturn(0);

        $this->__invoke(1, RefundType::orderItemUnit())->shouldReturn(false);
    }

    function it_returns_true_if_remaining_order_item_unit_total_is_more_than_0(
        RemainingTotalProviderInterface $remainingOrderItemUnitTotalProvider
    ): void {
        $remainingOrderItemUnitTotalProvider->getTotalLeftToRefund(1)->willReturn(100);

        $this->__invoke(1, RefundType::orderItemUnit())->shouldReturn(true);
    }
}

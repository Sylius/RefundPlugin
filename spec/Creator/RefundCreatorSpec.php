<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefundedException;
use Sylius\RefundPlugin\Exception\UnitRefundExceededException;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundCreatorSpec extends ObjectBehavior
{
    function let(
        RefundFactoryInterface $refundFactory,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        RemainingTotalProviderInterface $remainingTotalProvider,
        ObjectManager $refundEntityManager
    ): void {
        $this->beConstructedWith(
            $refundFactory,
            $unitRefundingAvailabilityChecker,
            $remainingTotalProvider,
            $refundEntityManager
        );
    }

    function it_implements_refund_creator_interface(): void
    {
        $this->shouldImplement(RefundCreatorInterface::class);
    }

    function it_creates_refund_with_given_data_and_save_it_in_database(
        RefundFactoryInterface $refundFactory,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        RemainingTotalProviderInterface $remainingTotalProvider,
        ObjectManager $refundEntityManager,
        RefundInterface $refund
    ): void {
        $refundType = RefundType::shipment();

        $unitRefundingAvailabilityChecker->__invoke(1, $refundType)->willReturn(true);
        $remainingTotalProvider->getTotalLeftToRefund(1)->willReturn(1000);

        $refundFactory->createWithData('000222', 1, 1000, RefundType::shipment())->willReturn($refund);

        $refundEntityManager->persist($refund)->shouldBeCalled();
        $refundEntityManager->flush()->shouldBeCalled();

        $this('000222', 1, 1000, $refundType);
    }

    function it_throws_exception_if_unit_has_already_been_refunded(
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker
    ): void {
        $refundType = RefundType::shipment();

        $unitRefundingAvailabilityChecker->__invoke(1, $refundType)->willReturn(false);

        $this
            ->shouldThrow(UnitAlreadyRefundedException::class)
            ->during('__invoke', ['000222', 1, 1000, $refundType])
        ;
    }

    function it_throws_exception_if_order_item_unit_refund_amount_is_too_big(
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        RemainingTotalProviderInterface $remainingTotalProvider
    ): void {
        $refundType = RefundType::orderItemUnit();

        $unitRefundingAvailabilityChecker->__invoke(1, $refundType)->willReturn(true);
        $remainingTotalProvider->getTotalLeftToRefund(1)->willReturn(500);

        $this
            ->shouldThrow(UnitRefundExceededException::class)
            ->during('__invoke', ['000222', 1, 1000, $refundType])
        ;
    }
}

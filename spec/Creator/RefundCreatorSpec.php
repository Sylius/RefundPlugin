<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefundedException;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;

final class RefundCreatorSpec extends ObjectBehavior
{
    function let(
        RefundFactoryInterface $refundFactory,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        ObjectManager $refundManager
    ): void {
        $this->beConstructedWith($refundFactory, $unitRefundingAvailabilityChecker, $refundManager);
    }

    function it_implements_refund_creator_interface()
    {
        $this->shouldImplement(RefundCreatorInterface::class);
    }

    function it_creates_refund_with_given_data_and_save_it_in_database(
        RefundFactoryInterface $refundFactory,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        ObjectManager $refundManager,
        RefundInterface $refund
    ) {
        $unitRefundingAvailabilityChecker->__invoke(1)->willReturn(true);

        $refundFactory->createWithData('000222', 1, 1000)->willReturn($refund);

        $refundManager->persist($refund)->shouldBeCalled();
        $refundManager->flush()->shouldBeCalled();

        $this('000222', 1, 1000);
    }

    function it_throws_exception_if_unit_has_already_been_refunded(
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker
    ) {
        $unitRefundingAvailabilityChecker->__invoke(1)->willReturn(false);

        $this
            ->shouldThrow(UnitAlreadyRefundedException::class)
            ->during('__invoke', ['000222', 1, 1000])
        ;
    }
}

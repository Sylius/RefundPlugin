<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;

final class UnitRefundingAvailabilityCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $refundRepository): void
    {
        $this->beConstructedWith($refundRepository);
    }

    function it_implements_unit_refunding_availability_checker_interface(): void
    {
        $this->shouldImplement(UnitRefundingAvailabilityCheckerInterface::class);
    }

    function it_returns_true_if_refund_for_given_unit_does_not_already_exist(RepositoryInterface $refundRepository): void
    {
        $refundRepository->findOneBy(['refundedUnitId' => 1])->willReturn(null);

        $this->__invoke(1)->shouldReturn(true);
    }

    function it_returns_false_if_refund_for_given_unit_does_not_already_exist(
        RepositoryInterface $refundRepository,
        RefundInterface $refund
    ): void {
        $refundRepository->findOneBy(['refundedUnitId' => 1])->willReturn($refund);

        $this->__invoke(1)->shouldReturn(false);
    }
}

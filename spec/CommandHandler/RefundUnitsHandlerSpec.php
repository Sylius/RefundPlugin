<?php

namespace spec\Sylius\RefundPlugin\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class RefundUnitsHandlerSpec extends ObjectBehavior
{
    function let(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider
    ): void {
        $this->beConstructedWith($refundCreator, $refundedUnitTotalProvider);
    }

    function it_handles_command_and_create_refund_for_each_refunded_unit(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider
    ): void {
        $refundedUnitTotalProvider->getTotalOfUnitWithId(1)->willReturn(1000);
        $refundedUnitTotalProvider->getTotalOfUnitWithId(3)->willReturn(500);

        $refundCreator->__invoke('000222', 1, 1000)->shouldBeCalled();
        $refundCreator->__invoke('000222', 3, 500)->shouldBeCalled();

        $this->handle(new RefundUnits('000222', [1, 3]));
    }
}

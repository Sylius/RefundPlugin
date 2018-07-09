<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class RefundUnitsHandlerSpec extends ObjectBehavior
{
    function let(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        EventBus $eventBus
    ): void {
        $this->beConstructedWith($refundCreator, $refundedUnitTotalProvider, $eventBus);
    }

    function it_handles_command_and_create_refund_for_each_refunded_unit(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        EventBus $eventBus
    ): void {
        $refundedUnitTotalProvider->getTotalOfUnitWithId(1)->willReturn(1000);
        $refundedUnitTotalProvider->getTotalOfUnitWithId(3)->willReturn(500);

        $refundCreator->__invoke('000222', 1, 1000)->shouldBeCalled();
        $refundCreator->__invoke('000222', 3, 500)->shouldBeCalled();

        $eventBus->dispatch(Argument::that(function (UnitsRefunded $event): bool {
            return
                $event->orderNumber() === '000222' &&
                $event->unitIds() === [1, 3] &&
                $event->amount() === 1500
            ;
        }))->shouldBeCalled();

        $this(new RefundUnits('000222', [1, 3]));
    }
}

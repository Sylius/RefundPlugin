<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Refunder;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;
use Sylius\RefundPlugin\Refunder\RefunderInterface;

final class OrderUnitsRefunderSpec extends ObjectBehavior
{
    function let(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        EventBus $eventBus
    ): void {
        $this->beConstructedWith($refundCreator, $refundedUnitTotalProvider, $eventBus);
    }

    function it_implements_refunder_interface(): void
    {
        $this->shouldImplement(RefunderInterface::class);
    }

    function it_creates_refund_for_each_unit_and_dispatch_proper_event(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        EventBus $eventBus
    ): void {
        $refundedUnitTotalProvider->getTotalOfUnitWithId(1)->willReturn(1500);
        $refundedUnitTotalProvider->getTotalOfUnitWithId(3)->willReturn(1000);

        $refundCreator->__invoke('000222', 1, 1500)->shouldBeCalled();

        $eventBus->dispatch(Argument::that(function (UnitRefunded $event): bool {
            return
                $event->orderNumber() === '000222' &&
                $event->unitId() === 1 &&
                $event->amount() === 1500
            ;
        }))->shouldBeCalled();

        $refundCreator->__invoke('000222', 3, 1000)->shouldBeCalled();

        $eventBus->dispatch(Argument::that(function (UnitRefunded $event): bool {
            return
                $event->orderNumber() === '000222' &&
                $event->unitId() === 3 &&
                $event->amount() === 1000
            ;
        }))->shouldBeCalled();

        $this->refundFromOrder([1, 3], '000222')->shouldReturn(2500);;
    }
}

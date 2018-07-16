<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class RefundUnitsHandlerSpec extends ObjectBehavior
{
    function let(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        EventBus $eventBus
    ): void {
        $this->beConstructedWith(
            $refundCreator,
            $refundedUnitTotalProvider,
            $orderRefundingAvailabilityChecker,
            $eventBus
        );
    }

    function it_handles_command_and_create_refund_for_each_refunded_unit(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        EventBus $eventBus
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('000222')->willReturn(true);

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

    function it_throws_an_exception_if_order_is_not_available_for_refund(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('000222')->willReturn(false);

        $this
            ->shouldThrow(OrderNotAvailableForRefundingException::class)
            ->during('__invoke', [new RefundUnits('000222', [1, 3])])
        ;
    }
}

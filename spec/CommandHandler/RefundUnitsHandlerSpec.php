<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Refunder\RefunderInterface;

final class RefundUnitsHandlerSpec extends ObjectBehavior
{
    function let(
        RefunderInterface $orderItemUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith(
            $orderItemUnitsRefunder,
            $orderShipmentsRefunder,
            $orderRefundingAvailabilityChecker,
            $eventBus,
            $orderRepository
        );
    }

    function it_handles_command_and_create_refund_for_each_refunded_unit(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefunderInterface $orderItemUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): void {
        $unitRefunds = [new OrderItemUnitRefund(1, 3000), new OrderItemUnitRefund(3, 4000)];
        $shipmentRefunds = [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)];

        $orderRefundingAvailabilityChecker->__invoke('000222')->willReturn(true);

        $orderItemUnitsRefunder->refundFromOrder($unitRefunds, '000222')->willReturn(3000);
        $orderShipmentsRefunder->refundFromOrder($shipmentRefunds, '000222')->willReturn(4000);

        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $order->getCurrencyCode()->willReturn('USD');

        $eventBus->dispatch(Argument::that(function (UnitsRefunded $event) use ($unitRefunds, $shipmentRefunds): bool {
            return
                $event->orderNumber() === '000222' &&
                $event->units() === $unitRefunds &&
                $event->shipments() === $shipmentRefunds &&
                $event->amount() === 7000 &&
                $event->paymentMethodId() === 1 &&
                $event->comment() === 'Comment'
            ;
        }))->shouldBeCalled();

        $this(new RefundUnits('000222', $unitRefunds, $shipmentRefunds, 1, 'Comment'));
    }

    function it_changes_order_state_to_fully_refunded_when_whole_order_total_is_refunded(
        RefunderInterface $orderItemUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): void {
        $unitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 500)];
        $shipmentRefunds = [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)];

        $orderRefundingAvailabilityChecker->__invoke('000222')->willReturn(true);

        $orderItemUnitsRefunder->refundFromOrder($unitRefunds, '000222')->willReturn(1000);
        $orderShipmentsRefunder->refundFromOrder($shipmentRefunds, '000222')->willReturn(500);

        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $order->getCurrencyCode()->willReturn('USD');

        $eventBus->dispatch(Argument::that(function (UnitsRefunded $event) use ($unitRefunds, $shipmentRefunds): bool {
            return
                $event->orderNumber() === '000222' &&
                $event->units() === $unitRefunds &&
                $event->shipments() === $shipmentRefunds &&
                $event->amount() === 1500 &&
                $event->paymentMethodId() === 1 &&
                $event->comment() === 'Comment'
            ;
        }))->shouldBeCalled();

        $this(new RefundUnits('000222', $unitRefunds, $shipmentRefunds, 1, 'Comment'));
    }

    function it_throws_an_exception_if_order_is_not_available_for_refund(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('000222')->willReturn(false);

        $this
            ->shouldThrow(OrderNotAvailableForRefundingException::class)
            ->during('__invoke', [new RefundUnits(
                '000222',
                [new OrderItemUnitRefund(1, 3000), new OrderItemUnitRefund(3, 4000)],
                [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)],
                1,
                'Comment'),
            ])
        ;
    }
}

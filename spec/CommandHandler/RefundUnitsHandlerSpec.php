<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefund;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;

final class RefundUnitsHandlerSpec extends ObjectBehavior
{
    function let(
        RefunderInterface $orderItemUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith(
            $orderItemUnitsRefunder,
            $orderShipmentsRefunder,
            $refundUnitsCommandValidator,
            $eventBus,
            $orderRepository
        );
    }

    function it_handles_command_and_create_refund_for_each_refunded_unit(
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator,
        RefunderInterface $orderItemUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): void {
        $unitRefunds = [new UnitRefund(1, 3000), new UnitRefund(3, 4000)];
        $shipmentRefunds = [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)];

        $command = new RefundUnits('000222', $unitRefunds, $shipmentRefunds, 1, 'Comment');

        $refundUnitsCommandValidator->validate($command)->shouldBeCalled();

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

        $this($command);
    }

    function it_changes_order_state_to_fully_refunded_when_whole_order_total_is_refunded(
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator,
        RefunderInterface $orderItemUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): void {
        $unitRefunds = [new UnitRefund(1, 1000), new UnitRefund(3, 500)];
        $shipmentRefunds = [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)];

        $command = new RefundUnits('000222', $unitRefunds, $shipmentRefunds, 1, 'Comment');

        $refundUnitsCommandValidator->validate($command)->shouldBeCalled();

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

        $this($command);
    }

    function it_does_nothing_if_command_is_not_valid(
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator,
        RefunderInterface $orderItemUnitsRefunder
    ): void {
        $command = new RefundUnits(
            '000222',
            [new UnitRefund(1, 3000), new UnitRefund(3, 4000)],
            [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)],
            1,
            'Comment')
        ;

        $refundUnitsCommandValidator->validate($command)->willThrow(\Exception::class);
        $orderItemUnitsRefunder->refundFromOrder(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\Exception::class)
            ->during('__invoke', [$command])
        ;
    }
}

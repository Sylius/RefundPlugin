<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefunding;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundUnitsHandlerSpec extends ObjectBehavior
{
    function let(
        RefunderInterface $orderItemUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator
    ): void {
        $this->beConstructedWith(
            $orderItemUnitsRefunder,
            $orderShipmentsRefunder,
            $eventBus,
            $orderRepository,
            $refundUnitsCommandValidator
        );
    }

    function it_handles_command_and_create_refund_for_each_refunded_unit(
        RefunderInterface $orderItemUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator,
        OrderInterface $order
    ): void {
        $unitRefunds = [new OrderItemUnitRefund(1, 3000), new OrderItemUnitRefund(3, 4000)];
        $shipmentRefunds = [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)];

        $orderItemUnitsRefunder->refundFromOrder($unitRefunds, '000222')->willReturn(3000);
        $orderShipmentsRefunder->refundFromOrder($shipmentRefunds, '000222')->willReturn(4000);

        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $order->getCurrencyCode()->willReturn('USD');

        $refundUnitsCommandValidator->validate(Argument::type(RefundUnits::class))->shouldBeCalled();

        $event = new UnitsRefunded('000222', $unitRefunds, $shipmentRefunds, 1, 7000, 'USD', 'Comment');
        $eventBus->dispatch($event)->willReturn(new Envelope($event))->shouldBeCalled();

        $this(new RefundUnits('000222', $unitRefunds, $shipmentRefunds, 1, 'Comment'));
    }

    function it_throws_an_exception_if_order_is_not_available_for_refund(
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator
    ): void {
        $refundUnitsCommand = new RefundUnits('000222',
            [new OrderItemUnitRefund(1, 3000), new OrderItemUnitRefund(3, 4000)],
            [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)],
            1,
            'Comment'
        );

        $refundUnitsCommandValidator
            ->validate($refundUnitsCommand)
            ->willThrow(OrderNotAvailableForRefunding::withOrderNumber('000222'))
        ;

        $this
            ->shouldThrow(OrderNotAvailableForRefunding::withOrderNumber('000222'))
            ->during('__invoke', [$refundUnitsCommand])
        ;
    }
}

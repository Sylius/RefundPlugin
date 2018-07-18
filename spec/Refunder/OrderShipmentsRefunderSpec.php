<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Refunder;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Provider\RefundedShipmentFeeProviderInterface;
use Sylius\RefundPlugin\Refunder\RefunderInterface;

final class OrderShipmentsRefunderSpec extends ObjectBehavior
{
    function let(
        RefundCreatorInterface $refundCreator,
        RefundedShipmentFeeProviderInterface $refundedShipmentFeeProvider,
        EventBus $eventBus
    ): void {
        $this->beConstructedWith($refundCreator, $refundedShipmentFeeProvider, $eventBus);
    }

    function it_implements_refunder_interface(): void
    {
        $this->shouldImplement(RefunderInterface::class);
    }

    function it_creates_refund_for_each_shipment_and_dispatch_proper_event(
        RefundCreatorInterface $refundCreator,
        RefundedShipmentFeeProviderInterface $refundedShipmentFeeProvider,
        EventBus $eventBus
    ): void {
        $refundedShipmentFeeProvider->getFeeOfShipment(4)->willReturn(2500);

        $refundCreator->__invoke('000222', 4, 2500)->shouldBeCalled();

        $eventBus->dispatch(Argument::that(function (ShipmentRefunded $event): bool {
            return
                $event->orderNumber() === '000222' &&
                $event->shipmentUnitId() === 4 &&
                $event->amount() === 2500
            ;
        }))->shouldBeCalled();

        $this->refundFromOrder([4], '000222')->shouldReturn(2500);;
    }
}

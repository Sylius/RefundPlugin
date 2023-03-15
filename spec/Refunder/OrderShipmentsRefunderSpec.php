<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Refunder;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderShipmentsRefunderSpec extends ObjectBehavior
{
    function let(
        RefundCreatorInterface $refundCreator,
        MessageBusInterface $eventBus,
        UnitRefundFilterInterface $unitRefundFilter,
    ): void {
        $this->beConstructedWith($refundCreator, $eventBus, $unitRefundFilter);
    }

    function it_implements_refunder_interface(): void
    {
        $this->shouldImplement(RefunderInterface::class);
    }

    function it_creates_refund_for_each_shipment_and_dispatch_proper_event(
        RefundCreatorInterface $refundCreator,
        MessageBusInterface $eventBus,
        UnitRefundFilterInterface $unitRefundFilter,
    ): void {
        $shipmentRefund = new ShipmentRefund(4, 2500);
        $refunds = [$shipmentRefund, new OrderItemUnitRefund(8, 1000)];

        $unitRefundFilter->filterUnitRefunds($refunds, ShipmentRefund::class)->willReturn([$shipmentRefund]);

        $refundCreator->__invoke('000222', 4, 2500, RefundType::shipment())->shouldBeCalled();

        $event = new ShipmentRefunded('000222', 4, 2500);
        $eventBus->dispatch($event)->willReturn(new Envelope($event))->shouldBeCalled();

        $this->refundFromOrder($refunds, '000222')->shouldReturn(2500);
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_creates_refund_for_each_shipment_and_dispatch_proper_event_with_deprecations(
        RefundCreatorInterface $refundCreator,
        MessageBusInterface $eventBus,
    ): void {
        $this->beConstructedWith($refundCreator, $eventBus);

        $shipmentRefunds = [new ShipmentRefund(4, 2500)];

        $refundCreator->__invoke('000222', 4, 2500, RefundType::shipment())->shouldBeCalled();

        $event = new ShipmentRefunded('000222', 4, 2500);
        $eventBus->dispatch($event)->willReturn(new Envelope($event))->shouldBeCalled();

        $this->refundFromOrder($shipmentRefunds, '000222')->shouldReturn(2500);
    }
}

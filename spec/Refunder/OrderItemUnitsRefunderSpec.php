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
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemUnitsRefunderSpec extends ObjectBehavior
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

    function it_creates_refund_for_each_unit_and_dispatch_proper_event(
        RefundCreatorInterface $refundCreator,
        MessageBusInterface $eventBus,
        UnitRefundFilterInterface $unitRefundFilter,
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 1000);
        $shipmentRefund = new ShipmentRefund(3, 1000);

        $unitRefundFilter
            ->filterUnitRefunds([$firstUnitRefund, $secondUnitRefund, $shipmentRefund], OrderItemUnitRefund::class)
            ->willReturn([$firstUnitRefund, $secondUnitRefund])
        ;

        $refundCreator->__invoke('000222', 1, 1500, RefundType::orderItemUnit())->shouldBeCalled();

        $firstEvent = new UnitRefunded('000222', 1, 1500);
        $eventBus->dispatch($firstEvent)->willReturn(new Envelope($firstEvent))->shouldBeCalled();

        $refundCreator->__invoke('000222', 3, 1000, RefundType::orderItemUnit())->shouldBeCalled();

        $secondEvent = new UnitRefunded('000222', 3, 1000);
        $eventBus->dispatch($secondEvent)->willReturn(new Envelope($secondEvent))->shouldBeCalled();

        $this->refundFromOrder([$firstUnitRefund, $secondUnitRefund, $shipmentRefund], '000222')->shouldReturn(2500);
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_creates_refund_for_each_unit_and_dispatch_proper_event_with_deprecations(
        RefundCreatorInterface $refundCreator,
        MessageBusInterface $eventBus,
    ): void {
        $this->beConstructedWith($refundCreator, $eventBus);

        $firstUnitRefund = new OrderItemUnitRefund(1, 1500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 1000);

        $refundCreator->__invoke('000222', 1, 1500, RefundType::orderItemUnit())->shouldBeCalled();

        $firstEvent = new UnitRefunded('000222', 1, 1500);
        $eventBus->dispatch($firstEvent)->willReturn(new Envelope($firstEvent))->shouldBeCalled();

        $refundCreator->__invoke('000222', 3, 1000, RefundType::orderItemUnit())->shouldBeCalled();

        $secondEvent = new UnitRefunded('000222', 3, 1000);
        $eventBus->dispatch($secondEvent)->willReturn(new Envelope($secondEvent))->shouldBeCalled();

        $this->refundFromOrder([$firstUnitRefund, $secondUnitRefund], '000222')->shouldReturn(2500);
    }
}

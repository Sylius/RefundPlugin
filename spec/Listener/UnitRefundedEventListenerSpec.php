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

namespace spec\Sylius\RefundPlugin\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Event\UnitRefundedInterface;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface;

final class UnitRefundedEventListenerSpec extends ObjectBehavior
{
    function let(OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver): void
    {
        $this->beConstructedWith($orderPartiallyRefundedStateResolver);
    }

    function it_resolves_order_partially_refunded_state_with_unit_refunded_event(
        OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver,
    ): void {
        $orderPartiallyRefundedStateResolver->resolve('000777')->shouldBeCalled();

        $this->__invoke(new UnitRefunded('000777', 10, 1000));
    }

    function it_resolves_order_partially_refunded_state_with_shipment_refunded_event(
        OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver,
    ): void {
        $orderPartiallyRefundedStateResolver->resolve('000777')->shouldBeCalled();

        $this->__invoke(new ShipmentRefunded('000777', 10, 1000));
    }

    function it_resolves_order_partially_refunded_state_with_an_event_implementing_unit_refunded_interface(
        OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver,
        UnitRefundedInterface $unitRefunded,
    ): void {
        $orderPartiallyRefundedStateResolver->resolve('000777')->shouldBeCalled();

        $unitRefunded->orderNumber()->willReturn('000777');

        $this->__invoke($unitRefunded->getWrappedObject());
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface;

final class UnitRefundedEventListenerSpec extends ObjectBehavior
{
    function let(OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver): void
    {
        $this->beConstructedWith($orderPartiallyRefundedStateResolver);
    }

    function it_resolves_order_partially_refunded_state(
        OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver
    ): void {
        $orderPartiallyRefundedStateResolver->resolve('000777')->shouldBeCalled();

        $this->__invoke(new UnitRefunded('000777', 10, 1000));
    }
}

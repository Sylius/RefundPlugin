<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Command;

use PhpSpec\ObjectBehavior;

final class RefundUnitsSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_refund_specific_order_units_and_shipments(): void
    {
        $this->beConstructedWith('000222', [1, 3, 5], [2], 1);

        $this->orderNumber()->shouldReturn('000222');
        $this->unitIds()->shouldReturn([1, 3, 5]);
        $this->shipmentIds()->shouldReturn([2]);
        $this->paymentMethodId()->shouldReturn(1);
    }
}

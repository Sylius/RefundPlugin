<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;

final class ShipmentRefundedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_shipment_has_been_refunded(): void
    {
        $this->beConstructedWith('000222', 1, 1000);

        $this->orderNumber()->shouldReturn('000222');
        $this->shipmentUnitId()->shouldReturn(1);
        $this->amount()->shouldReturn(1000);
    }
}

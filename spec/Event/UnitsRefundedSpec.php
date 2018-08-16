<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;

final class UnitsRefundedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_units_and_shipments_has_been_refunded(): void
    {
        $this->beConstructedWith('000222', [1, 2, 3], [1], 1, 5000);

        $this->orderNumber()->shouldReturn('000222');
        $this->unitIds()->shouldReturn([1, 2, 3]);
        $this->shipmentIds()->shouldReturn([1]);
        $this->paymentMethodId()->shouldReturn(1);
        $this->amount()->shouldReturn(5000);
    }
}

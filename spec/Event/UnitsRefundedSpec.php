<?php

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;

final class UnitsRefundedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_units_has_been_refunded(): void
    {
        $this->beConstructedWith('000222', [1, 2, 3], 5000);

        $this->orderNumber()->shouldReturn('000222');
        $this->unitIds()->shouldReturn([1, 2, 3]);
        $this->amount()->shouldReturn(5000);
    }
}

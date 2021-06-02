<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;

final class UnitRefundedSpec extends ObjectBehavior
{
    public function it_represents_an_immutable_fact_that_unit_has_been_refunded(): void
    {
        $this->beConstructedWith('000222', 1, 1000);

        $this->orderNumber()->shouldReturn('000222');
        $this->unitId()->shouldReturn(1);
        $this->amount()->shouldReturn(1000);
    }
}

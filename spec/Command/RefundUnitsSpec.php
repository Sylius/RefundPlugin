<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Command;

use PhpSpec\ObjectBehavior;

final class RefundUnitsSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_refund_specific_order_units(): void
    {
        $this->beConstructedWith('000222', [1, 3, 5]);

        $this->orderNumber()->shouldReturn('000222');
        $this->refundedUnitIds()->shouldReturn([1, 3, 5]);
    }
}

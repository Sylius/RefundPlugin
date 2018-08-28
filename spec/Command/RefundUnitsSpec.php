<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\UnitRefund;

final class RefundUnitsSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_refund_specific_order_units_and_shipments(): void
    {
        $unitRefunds = [new UnitRefund(1, 1000), new UnitRefund(3, 2000), new UnitRefund(5, 3000)];

        $this->beConstructedWith('000222', $unitRefunds, [2], 1, 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->units()->shouldReturn($unitRefunds);
        $this->shipmentIds()->shouldReturn([2]);
        $this->paymentMethodId()->shouldReturn(1);
        $this->comment()->shouldReturn('Comment');
    }
}

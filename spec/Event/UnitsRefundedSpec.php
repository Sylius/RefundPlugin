<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\UnitRefund;

final class UnitsRefundedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_units_and_shipments_has_been_refunded(): void
    {
        $unitRefunds = [new UnitRefund(1, 1000), new UnitRefund(3, 2000), new UnitRefund(5, 3000)];

        $this->beConstructedWith('000222', $unitRefunds, [1], 1, 5000, 'USD', 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->units()->shouldReturn($unitRefunds);
        $this->shipmentIds()->shouldReturn([1]);
        $this->paymentMethodId()->shouldReturn(1);
        $this->amount()->shouldReturn(5000);
        $this->currencyCode()->shouldReturn('USD');
        $this->comment()->shouldReturn('Comment');
    }
}

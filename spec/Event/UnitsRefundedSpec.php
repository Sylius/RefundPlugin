<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;

final class UnitsRefundedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_units_and_shipments_has_been_refunded(): void
    {
        $unitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];
        $shipmentRefunds = [new ShipmentRefund(3, 500), new ShipmentRefund(4, 1000)];

        $this->beConstructedWith('000222', $unitRefunds, $shipmentRefunds, 1, 5000, 'USD', 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->units()->shouldReturn($unitRefunds);
        $this->shipments()->shouldReturn($shipmentRefunds);
        $this->paymentMethodId()->shouldReturn(1);
        $this->amount()->shouldReturn(5000);
        $this->currencyCode()->shouldReturn('USD');
        $this->comment()->shouldReturn('Comment');
    }
}

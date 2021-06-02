<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class RefundUnitsSpec extends ObjectBehavior
{
    public function it_represents_an_intention_to_refund_specific_order_units_and_shipments(): void
    {
        $unitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];
        $shipmentRefunds = [new ShipmentRefund(1, 1000)];

        $this->beConstructedWith('000222', $unitRefunds, $shipmentRefunds, 1, 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->units()->shouldReturn($unitRefunds);
        $this->shipments()->shouldReturn($shipmentRefunds);
        $this->paymentMethodId()->shouldReturn(1);
        $this->comment()->shouldReturn('Comment');
    }
}

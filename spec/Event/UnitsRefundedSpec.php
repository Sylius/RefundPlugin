<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class UnitsRefundedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_units_and_shipments_has_been_refunded(): void
    {
        $unitsRefunds = [
            new OrderItemUnitRefund(1, 1000),
            new ShipmentRefund(3, 500),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(4, 1000),
        ];

        $this->beConstructedWith('000222', $unitsRefunds, 1, 5000, 'USD', 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->units()->shouldReturn($unitsRefunds);
        $this->paymentMethodId()->shouldReturn(1);
        $this->amount()->shouldReturn(5000);
        $this->currencyCode()->shouldReturn('USD');
        $this->comment()->shouldReturn('Comment');
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_represents_an_immutable_fact_that_units_and_shipments_has_been_refunded_with_deprecations(): void
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

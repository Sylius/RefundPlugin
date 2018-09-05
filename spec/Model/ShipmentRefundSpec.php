<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Model;

use PhpSpec\ObjectBehavior;

final class ShipmentRefundSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(1, 1000);
    }

    function it_has_shipment_id(): void
    {
        $this->shipmentId()->shouldReturn(1);
    }

    function it_has_total(): void
    {
        $this->total()->shouldReturn(1000);
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Model;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class OrderItemUnitRefundSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(1, 1000);
    }

    function it_implements_unit_refund_interface(): void
    {
        $this->shouldImplement(UnitRefundInterface::class);
    }

    function it_has_shipment_id(): void
    {
        $this->id()->shouldReturn(1);
    }

    function it_has_total(): void
    {
        $this->total()->shouldReturn(1000);
    }
}

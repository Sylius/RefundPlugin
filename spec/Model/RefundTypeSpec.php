<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Model;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Exception\RefundTypeNotResolved;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundTypeSpec extends ObjectBehavior
{
    function it_can_be_order_item_unit_type(): void
    {
        $this->beConstructedThrough('orderItemUnit');
        $this->__toString()->shouldReturn(RefundType::ORDER_ITEM_UNIT);
    }

    function it_can_be_shipment_type(): void
    {
        $this->beConstructedThrough('shipment');
        $this->__toString()->shouldReturn(RefundType::SHIPMENT);
    }

    function it_can_equals_another_refund_type(): void
    {
        $this->beConstructedThrough('shipment');
        $this->equals(RefundType::shipment())->shouldReturn(true);
        $this->equals(RefundType::orderItemUnit())->shouldReturn(false);
    }

    function it_throws_exception_if_passed_refund_type_cannot_be_resolved(): void
    {
        $this
            ->shouldThrow(RefundTypeNotResolved::withType('test'))
            ->during('__construct', ['test'])
        ;
    }
}

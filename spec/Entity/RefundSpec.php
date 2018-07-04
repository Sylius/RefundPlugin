<?php

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\RefundInterface;

final class RefundSpec extends ObjectBehavior
{
    function it_implements_refund_interface()
    {
        $this->shouldImplement(RefundInterface::class);
    }

    function it_has_order_number()
    {
        $this->setOrderNumber('000666');
        $this->getOrderNumber()->shouldReturn('000666');
    }

    function it_has_amount()
    {
        $this->setAmount(1000);
        $this->getAmount()->shouldReturn(1000);
    }

    function it_has_refunded_unit_id()
    {
        $this->setRefundedUnitId(3);
        $this->getRefundedUnitId()->shouldReturn(3);
    }
}

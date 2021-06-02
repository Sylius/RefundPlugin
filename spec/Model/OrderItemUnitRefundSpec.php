<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Model;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class OrderItemUnitRefundSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(1, 1000);
    }

    public function it_implements_unit_refund_interface(): void
    {
        $this->shouldImplement(UnitRefundInterface::class);
    }

    public function it_has_id(): void
    {
        $this->id()->shouldReturn(1);
    }

    public function it_has_total(): void
    {
        $this->total()->shouldReturn(1000);
    }
}

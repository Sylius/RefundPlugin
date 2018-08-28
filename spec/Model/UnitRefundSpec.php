<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Model;

use PhpSpec\ObjectBehavior;

final class UnitRefundSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(1, 1000);
    }

    function it_has_unit_id(): void
    {
        $this->unitId()->shouldReturn(1);
    }

    function it_has_total(): void
    {
        $this->total()->shouldReturn(1000);
    }
}

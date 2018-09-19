<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;

final class GenerateCreditMemoSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_generate_credit_memo(): void
    {
        $unitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];

        $this->beConstructedWith('000222', 1000, $unitRefunds, [3, 4], 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->total()->shouldReturn(1000);
        $this->units()->shouldReturn($unitRefunds);
        $this->shipments()->shouldReturn([3, 4]);
        $this->comment()->shouldReturn('Comment');
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Command;

use PhpSpec\ObjectBehavior;

final class GenerateCreditMemoSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_generate_credit_memo(): void
    {
        $this->beConstructedWith('000222', 1000, [1, 2], [3, 4], 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->total()->shouldReturn(1000);
        $this->unitIds()->shouldReturn([1, 2]);
        $this->shipmentIds()->shouldReturn([3, 4]);
        $this->comment()->shouldReturn('Comment');
    }
}

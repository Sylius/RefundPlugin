<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Command;

use PhpSpec\ObjectBehavior;

final class GenerateCreditMemoSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_generate_credit_memo(): void
    {
        $this->beConstructedWith('000222', 1000);

        $this->orderNumber()->shouldReturn('000222');
        $this->total()->shouldReturn(1000);
    }
}

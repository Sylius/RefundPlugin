<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;

final class CreditMemoGeneratedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_credit_memo_has_been_generated(): void
    {
        $this->beConstructedWith('000222');

        $this->orderNumber()->shouldReturn('000222');
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;

final class CreditMemoGeneratedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_credit_memo_has_been_generated(): void
    {
        $this->beConstructedWith('2018/01/000001', '000222');

        $this->number()->shouldReturn('2018/01/000001');
        $this->orderNumber()->shouldReturn('000222');
    }
}

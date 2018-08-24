<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;

final class CreditMemoChannelSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('WEB_US', 'United States');
    }

    function it_has_code(): void
    {
        $this->code()->shouldReturn('WEB_US');
    }

    function it_has_name(): void
    {
        $this->name()->shouldReturn('United States');
    }
}

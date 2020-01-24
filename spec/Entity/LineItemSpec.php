<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;

final class LineItemSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('Mjolnir', 2, 1000, 1100, 2000, 2200, 200, '10%');
    }

    function it_implements_line_item_interface(): void
    {
        $this->shouldImplement(LineItemInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_has_proper_line_item_data(): void
    {
        $this->name()->shouldReturn('Mjolnir');
        $this->quantity()->shouldReturn(2);
        $this->unitNetPrice()->shouldReturn(1000);
        $this->unitGrossPrice()->shouldReturn(1100);
        $this->netValue()->shouldReturn(2000);
        $this->grossValue()->shouldReturn(2200);
        $this->taxAmount()->shouldReturn(200);
        $this->taxRate()->shouldReturn('10%');
    }
}

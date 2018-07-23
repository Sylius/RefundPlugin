<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\SequenceInterface;

final class CreditMemoSequenceSpec extends ObjectBehavior
{
    function it_implements_sequence_interface(): void
    {
        $this->shouldImplement(SequenceInterface::class);
    }

    function it_has_incrementable_index(): void
    {
        $this->getIndex()->shouldReturn(0);

        $this->incrementIndex();
        $this->incrementIndex();

        $this->getIndex()->shouldReturn(2);
    }

    function it_has_version(): void
    {
        $this->getVersion()->shouldReturn(1);

        $this->setVersion(2);
        $this->getVersion()->shouldReturn(2);
    }
}

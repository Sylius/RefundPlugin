<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoSequence;
use Sylius\RefundPlugin\Factory\SequenceFactoryInterface;

final class CreditMemoSequenceFactorySpec extends ObjectBehavior
{
    function it_implements_sequence_factory_interface(): void
    {
        $this->shouldImplement(SequenceFactoryInterface::class);
    }

    function it_creates_new_credit_memo_sequence(): void
    {
        $this->createNew()->shouldBeLike(new CreditMemoSequence());
    }
}

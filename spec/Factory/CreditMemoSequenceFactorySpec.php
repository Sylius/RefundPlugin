<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoSequence;
use Sylius\RefundPlugin\Factory\CreditMemoSequenceFactoryInterface;

final class CreditMemoSequenceFactorySpec extends ObjectBehavior
{
    function it_implements_sequence_factory_interface(): void
    {
        $this->shouldImplement(CreditMemoSequenceFactoryInterface::class);
    }

    function it_creates_new_credit_memo_sequence(): void
    {
        $this->createNew()->shouldBeLike(new CreditMemoSequence());
    }
}

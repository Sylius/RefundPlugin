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

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoSequenceInterface;

final class CreditMemoSequenceSpec extends ObjectBehavior
{
    public function it_implements_credit_memo_sequence_interface(): void
    {
        $this->shouldImplement(CreditMemoSequenceInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_incrementable_index(): void
    {
        $this->getIndex()->shouldReturn(0);

        $this->incrementIndex();
        $this->incrementIndex();

        $this->getIndex()->shouldReturn(2);
    }

    public function it_has_version(): void
    {
        $this->getVersion()->shouldReturn(1);

        $this->setVersion(2);
        $this->getVersion()->shouldReturn(2);
    }
}

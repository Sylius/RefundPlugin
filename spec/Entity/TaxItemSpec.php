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
use Sylius\RefundPlugin\Entity\TaxItemInterface;

final class TaxItemSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('VAT', 100);
    }

    public function it_implements_tax_item_interface(): void
    {
        $this->shouldImplement(TaxItemInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
        $this->id()->shouldReturn(null);
    }

    public function it_has_a_label(): void
    {
        $this->label()->shouldReturn('VAT');
    }

    public function it_has_an_amount(): void
    {
        $this->amount()->shouldReturn(100);
    }
}

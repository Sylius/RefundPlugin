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
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Exception\LineItemsCannotBeMerged;

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

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
        $this->id()->shouldReturn(null);
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

    function it_merges_with_another_line_item(LineItemInterface $newLineItem): void
    {
        $newLineItem->name()->willReturn('Mjolnir');
        $newLineItem->quantity()->willReturn(1);
        $newLineItem->unitNetPrice()->willReturn(1000);
        $newLineItem->unitGrossPrice()->willReturn(1100);
        $newLineItem->netValue()->willReturn(1000);
        $newLineItem->grossValue()->willReturn(1100);
        $newLineItem->taxAmount()->willReturn(100);
        $newLineItem->taxRate()->willReturn('10%');

        $this->merge($newLineItem);

        $this->quantity()->shouldReturn(3);
        $this->netValue()->shouldReturn(3000);
        $this->grossValue()->shouldReturn(3300);
        $this->taxAmount()->shouldReturn(300);
    }

    function it_throws_an_exception_if_another_line_item_is_different_during_merging(LineItemInterface $newLineItem): void
    {
        $newLineItem->name()->willReturn('Stormbreaker');
        $newLineItem->unitNetPrice()->willReturn(1000);
        $newLineItem->unitGrossPrice()->willReturn(1100);
        $newLineItem->taxRate()->willReturn('10%');

        $this->shouldThrow(LineItemsCannotBeMerged::class)->during('merge', [$newLineItem]);
    }

    function it_compares_with_another_line_item(LineItemInterface $theSameLineItem, LineItemInterface $differentLineItem): void
    {
        $theSameLineItem->name()->willReturn('Mjolnir');
        $theSameLineItem->unitNetPrice()->willReturn(1000);
        $theSameLineItem->unitGrossPrice()->willReturn(1100);
        $theSameLineItem->taxRate()->willReturn('10%');

        $differentLineItem->name()->willReturn('Stormbreaker');
        $differentLineItem->unitNetPrice()->willReturn(1000);
        $differentLineItem->unitGrossPrice()->willReturn(1100);
        $differentLineItem->taxRate()->willReturn('10%');

        $this->compare($theSameLineItem)->shouldReturn(true);
        $this->compare($differentLineItem)->shouldReturn(false);
    }
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;

final class LineItemFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(LineItem::class);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_throws_an_exception_when_trying_to_create_a_new_line_item_without_data(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createNew')
        ;
    }

    function it_creates_a_new_line_item_without_tax_rate(): void
    {
        $this
            ->createWithData('T-Shirt', 2, 1000, 1200, 2000, 2400, 400)
            ->shouldHaveType(LineItem::class)
        ;
    }

    function it_creates_a_new_line_item_with_all_data(): void
    {
        $this
            ->createWithData('T-Shirt', 2, 1000, 1200, 2000, 2400, 400, '0.2')
            ->shouldHaveType(LineItem::class)
        ;
    }
}

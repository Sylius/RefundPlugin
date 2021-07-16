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

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\TaxItem;
use Sylius\RefundPlugin\Generator\TaxItemsGeneratorInterface;

final class TaxItemsGeneratorSpec extends ObjectBehavior
{
    function it_implements_tax_items_generator_interface(): void
    {
        $this->shouldImplement(TaxItemsGeneratorInterface::class);
    }

    function it_generates_tax_items(LineItemInterface $firstLineItem, LineItemInterface $secondLineItem): void
    {
        $firstLineItem->taxRate()->willReturn('VAT');
        $firstLineItem->taxAmount()->willReturn(500);

        $secondLineItem->taxRate()->willReturn('VAT');
        $secondLineItem->taxAmount()->willReturn(800);

        $this
            ->generate([$firstLineItem->getWrappedObject(), $secondLineItem->getWrappedObject()])
            ->shouldBeLike([new TaxItem('VAT', 1300)])
        ;
    }
}

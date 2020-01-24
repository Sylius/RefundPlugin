<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
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
            ->generate(new ArrayCollection([$firstLineItem->getWrappedObject(), $secondLineItem->getWrappedObject()]))
            ->shouldBeLike([new TaxItem('VAT', 1300)])
        ;
    }
}

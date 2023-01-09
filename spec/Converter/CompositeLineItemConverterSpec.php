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

namespace spec\Sylius\RefundPlugin\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Converter\LineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class CompositeLineItemConverterSpec extends ObjectBehavior
{
    function let(
        LineItemsConverterInterface $firstLineItemsConverter,
        LineItemsConverterInterface $secondLineItemsConverter,
    ): void {
        $this->beConstructedWith([$firstLineItemsConverter, $secondLineItemsConverter]);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_uses_all_line_items_converters_to_provide_line_items(
        LineItemsConverterInterface $firstLineItemsConverter,
        LineItemsConverterInterface $secondLineItemsConverter,
        UnitRefundInterface $firstUnitRefund,
        UnitRefundInterface $secondUnitRefund,
        UnitRefundInterface $thirdUnitRefund,
        UnitRefundInterface $fourthUnitRefund,
        LineItemInterface $firstLineItem,
        LineItemInterface $secondLineItem,
        LineItemInterface $thirdLineItem,
    ): void {
        $firstLineItemsConverter
            ->convert([$firstUnitRefund, $secondUnitRefund, $thirdUnitRefund, $fourthUnitRefund])
            ->willReturn([$firstLineItem])
        ;

        $secondLineItemsConverter
            ->convert([$firstUnitRefund, $secondUnitRefund, $thirdUnitRefund, $fourthUnitRefund])
            ->willReturn([$secondLineItem, $thirdLineItem])
        ;

        $this
            ->convert([$firstUnitRefund, $secondUnitRefund, $thirdUnitRefund, $fourthUnitRefund])
            ->shouldReturn([$firstLineItem, $secondLineItem, $thirdLineItem])
        ;
    }
}

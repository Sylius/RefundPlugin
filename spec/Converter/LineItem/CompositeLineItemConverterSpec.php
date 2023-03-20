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

namespace spec\Sylius\RefundPlugin\Converter\LineItem;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterUnitRefundAwareInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class CompositeLineItemConverterSpec extends ObjectBehavior
{
    function let(
        LineItemsConverterUnitRefundAwareInterface $firstLineItemsConverter,
        LineItemsConverterUnitRefundAwareInterface $secondLineItemsConverter,
        UnitRefundFilterInterface $unitRefundFilter,
    ): void {
        $this->beConstructedWith([$firstLineItemsConverter, $secondLineItemsConverter], $unitRefundFilter);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_uses_all_line_items_converters_to_provide_line_items(
        LineItemsConverterUnitRefundAwareInterface $firstLineItemsConverter,
        LineItemsConverterUnitRefundAwareInterface $secondLineItemsConverter,
        UnitRefundFilterInterface $unitRefundFilter,
        UnitRefundInterface $unsupportedUnitRefund,
        LineItemInterface $firstLineItem,
        LineItemInterface $secondLineItem,
        LineItemInterface $thirdLineItem,
        LineItemInterface $fourthLineItem,
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new ShipmentRefund(1, 2000);
        $thirdUnitRefund = new ShipmentRefund(2, 3000);
        $fourthUnitRefund = new OrderItemUnitRefund(2, 500);

        $units = [$firstUnitRefund, $secondUnitRefund, $thirdUnitRefund, $fourthUnitRefund, $unsupportedUnitRefund];
        $orderItemUnits = [$firstUnitRefund, $fourthUnitRefund];
        $shipmentUnits = [$secondUnitRefund, $thirdUnitRefund];

        $unitRefundFilter
            ->filterUnitRefunds($units, OrderItemUnitRefund::class)
            ->willReturn($orderItemUnits)
        ;
        $unitRefundFilter
            ->filterUnitRefunds($units, ShipmentRefund::class)
            ->willReturn($shipmentUnits)
        ;

        $firstLineItemsConverter->getUnitRefundClass()->willReturn(OrderItemUnitRefund::class);
        $firstLineItemsConverter->convert($orderItemUnits)->willReturn([$firstLineItem, $secondLineItem]);

        $secondLineItemsConverter->getUnitRefundClass()->willReturn(ShipmentRefund::class);
        $secondLineItemsConverter->convert($shipmentUnits)->willReturn([$thirdLineItem, $fourthLineItem]);

        $this->convert($units)->shouldReturn([$firstLineItem, $secondLineItem, $thirdLineItem, $fourthLineItem]);
    }
}

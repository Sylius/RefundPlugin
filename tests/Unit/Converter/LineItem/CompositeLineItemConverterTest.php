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

namespace Tests\Sylius\RefundPlugin\Unit\Converter\LineItem;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Converter\LineItem\CompositeLineItemConverter;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterUnitRefundAwareInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class CompositeLineItemConverterTest extends TestCase
{
    private LineItemsConverterUnitRefundAwareInterface&MockObject $firstLineItemsConverter;

    private LineItemsConverterUnitRefundAwareInterface&MockObject $secondLineItemsConverter;

    private UnitRefundFilterInterface&MockObject $unitRefundFilter;

    private CompositeLineItemConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firstLineItemsConverter = $this->createMock(LineItemsConverterUnitRefundAwareInterface::class);
        $this->secondLineItemsConverter = $this->createMock(LineItemsConverterUnitRefundAwareInterface::class);
        $this->unitRefundFilter = $this->createMock(UnitRefundFilterInterface::class);

        $this->converter = new CompositeLineItemConverter(
            [$this->firstLineItemsConverter, $this->secondLineItemsConverter],
            $this->unitRefundFilter,
        );
    }

    #[Test]
    public function it_implements_line_items_converter_interface(): void
    {
        self::assertInstanceOf(LineItemsConverterInterface::class, $this->converter);
    }

    #[Test]
    public function it_uses_all_line_items_converters_to_provide_line_items(): void
    {
        $unsupportedUnitRefund = $this->createMock(UnitRefundInterface::class);
        $firstLineItem = $this->createMock(LineItemInterface::class);
        $secondLineItem = $this->createMock(LineItemInterface::class);
        $thirdLineItem = $this->createMock(LineItemInterface::class);
        $fourthLineItem = $this->createMock(LineItemInterface::class);

        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new ShipmentRefund(1, 2000);
        $thirdUnitRefund = new ShipmentRefund(2, 3000);
        $fourthUnitRefund = new OrderItemUnitRefund(2, 500);

        $units = [$firstUnitRefund, $secondUnitRefund, $thirdUnitRefund, $fourthUnitRefund, $unsupportedUnitRefund];
        $orderItemUnits = [$firstUnitRefund, $fourthUnitRefund];
        $shipmentUnits = [$secondUnitRefund, $thirdUnitRefund];

        $this->unitRefundFilter
            ->expects($this->exactly(2))
            ->method('filterUnitRefunds')
            ->willReturnMap([
                [$units, OrderItemUnitRefund::class, $orderItemUnits],
                [$units, ShipmentRefund::class, $shipmentUnits],
            ]);

        $this->firstLineItemsConverter
            ->expects(self::once())
            ->method('getUnitRefundClass')
            ->willReturn(OrderItemUnitRefund::class);

        $this->firstLineItemsConverter
            ->expects(self::once())
            ->method('convert')
            ->with($orderItemUnits)
            ->willReturn([$firstLineItem, $secondLineItem]);

        $this->secondLineItemsConverter
            ->expects(self::once())
            ->method('getUnitRefundClass')
            ->willReturn(ShipmentRefund::class);

        $this->secondLineItemsConverter
            ->expects(self::once())
            ->method('convert')
            ->with($shipmentUnits)
            ->willReturn([$thirdLineItem, $fourthLineItem]);

        $result = $this->converter->convert($units);

        self::assertEquals([$firstLineItem, $secondLineItem, $thirdLineItem, $fourthLineItem], $result);
    }
}

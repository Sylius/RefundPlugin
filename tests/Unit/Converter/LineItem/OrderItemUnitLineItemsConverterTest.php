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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Converter\LineItem\OrderItemUnitLineItemsConverter;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Factory\LineItemFactoryInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;

final class OrderItemUnitLineItemsConverterTest extends TestCase
{
    private RepositoryInterface&MockObject $orderItemUnitRepository;

    private TaxRateProviderInterface&MockObject $taxRateProvider;

    private LineItemFactoryInterface&MockObject $lineItemFactory;

    private OrderItemUnitLineItemsConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemUnitRepository = $this->createMock(RepositoryInterface::class);
        $this->taxRateProvider = $this->createMock(TaxRateProviderInterface::class);
        $this->lineItemFactory = $this->createMock(LineItemFactoryInterface::class);

        $this->converter = new OrderItemUnitLineItemsConverter(
            $this->orderItemUnitRepository,
            $this->taxRateProvider,
            $this->lineItemFactory,
        );
    }

    /** @test */
    public function it_implements_line_items_converter_interface(): void
    {
        self::assertInstanceOf(LineItemsConverterInterface::class, $this->converter);
    }

    /** @test */
    public function it_converts_unit_refunds_to_line_items(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $lineItem = $this->createMock(LineItemInterface::class);

        $unitRefund = new OrderItemUnitRefund(1, 500);

        $this->orderItemUnitRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($orderItemUnit);

        $orderItemUnit
            ->expects(self::once())
            ->method('getOrderItem')
            ->willReturn($orderItem);

        $orderItemUnit
            ->expects($this->exactly(2))
            ->method('getTotal')
            ->willReturn(1500);

        $orderItemUnit
            ->expects(self::once())
            ->method('getTaxTotal')
            ->willReturn(300);

        $this->taxRateProvider
            ->expects(self::once())
            ->method('provide')
            ->with($orderItemUnit)
            ->willReturn('25%');

        $orderItem
            ->expects(self::once())
            ->method('getProductName')
            ->willReturn('Portal gun');

        $this->lineItemFactory
            ->expects(self::once())
            ->method('createWithData')
            ->with('Portal gun', 1, 400, 500, 400, 500, 100, '25%')
            ->willReturn($lineItem);

        $result = $this->converter->convert([$unitRefund]);

        self::assertEquals([$lineItem], $result);
    }

    /** @test */
    public function it_converts_unit_refunds_to_line_items_without_using_factory(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);

        $unitRefund = new OrderItemUnitRefund(1, 500);

        $this->lineItemFactory
            ->expects(self::once())
            ->method('createWithData')
            ->with('Portal gun', 1, 400, 500, 400, 500, 100, '25%')
            ->willReturn(new LineItem('Portal gun', 1, 400, 500, 400, 500, 100, '25%'));

        $this->orderItemUnitRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($orderItemUnit);

        $orderItemUnit
            ->expects(self::once())
            ->method('getOrderItem')
            ->willReturn($orderItem);

        $orderItemUnit
            ->expects($this->exactly(2))
            ->method('getTotal')
            ->willReturn(1500);

        $orderItemUnit
            ->expects(self::once())
            ->method('getTaxTotal')
            ->willReturn(300);

        $this->taxRateProvider
            ->expects(self::once())
            ->method('provide')
            ->with($orderItemUnit)
            ->willReturn('25%');

        $orderItem
            ->expects(self::once())
            ->method('getProductName')
            ->willReturn('Portal gun');

        $result = $this->converter->convert([$unitRefund]);

        self::assertEquals([new LineItem('Portal gun', 1, 400, 500, 400, 500, 100, '25%')], $result);
    }

    /** @test */
    public function it_groups_the_same_line_items_during_converting(): void
    {
        $firstOrderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $secondOrderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $firstOrderItem = $this->createMock(OrderItemInterface::class);
        $secondOrderItem = $this->createMock(OrderItemInterface::class);
        $firstLineItem = $this->createMock(LineItemInterface::class);
        $secondLineItem = $this->createMock(LineItemInterface::class);
        $thirdLineItem = $this->createMock(LineItemInterface::class);

        $firstUnitRefund = new OrderItemUnitRefund(1, 500);
        $secondUnitRefund = new OrderItemUnitRefund(2, 960);
        $thirdUnitRefund = new OrderItemUnitRefund(2, 960);

        $this->orderItemUnitRepository
            ->expects($this->exactly(3))
            ->method('find')
            ->willReturnMap([
                [1, $firstOrderItemUnit],
                [2, $secondOrderItemUnit],
            ]);

        $firstOrderItemUnit
            ->expects(self::once())
            ->method('getOrderItem')
            ->willReturn($firstOrderItem);

        $firstOrderItemUnit
            ->expects($this->exactly(2))
            ->method('getTotal')
            ->willReturn(1500);

        $firstOrderItemUnit
            ->expects(self::once())
            ->method('getTaxTotal')
            ->willReturn(300);

        $this->taxRateProvider
            ->expects($this->exactly(3))
            ->method('provide')
            ->willReturnMap([
                [$firstOrderItemUnit, '25%'],
                [$secondOrderItemUnit, '20%'],
            ]);

        $firstOrderItem
            ->expects(self::once())
            ->method('getProductName')
            ->willReturn('Portal gun');

        $secondOrderItemUnit
            ->expects($this->exactly(2))
            ->method('getOrderItem')
            ->willReturn($secondOrderItem);

        $secondOrderItemUnit
            ->expects($this->exactly(4))
            ->method('getTotal')
            ->willReturn(960);

        $secondOrderItemUnit
            ->expects($this->exactly(2))
            ->method('getTaxTotal')
            ->willReturn(160);

        $secondOrderItem
            ->expects($this->exactly(2))
            ->method('getProductName')
            ->willReturn('Space gun');

        $this->lineItemFactory
            ->expects($this->exactly(3))
            ->method('createWithData')
            ->willReturnMap([
                ['Portal gun', 1, 400, 500, 400, 500, 100, '25%', $firstLineItem],
                ['Space gun', 1, 800, 960, 800, 960, 160, '20%', $secondLineItem],
                ['Space gun', 1, 800, 960, 800, 960, 160, '20%', $thirdLineItem],
            ]);

        $firstLineItem
            ->expects($this->exactly(2))
            ->method('compare')
            ->willReturnMap([
                [$secondLineItem, false],
                [$thirdLineItem, false],
            ]);

        $secondLineItem
            ->expects(self::once())
            ->method('compare')
            ->with($thirdLineItem)
            ->willReturn(true);

        $secondLineItem
            ->expects(self::once())
            ->method('merge')
            ->with($thirdLineItem);

        $result = $this->converter->convert([$firstUnitRefund, $secondUnitRefund, $thirdUnitRefund]);

        self::assertEquals([$firstLineItem, $secondLineItem], $result);
    }

    /** @test */
    public function it_throws_an_error_if_one_of_units_is_not_order_item_unit_refund(): void
    {
        $unitRefund = new OrderItemUnitRefund(1, 500);
        $shipmentRefund = new ShipmentRefund(3, 1500);

        $this->expectException(\InvalidArgumentException::class);

        $this->converter->convert([$unitRefund, $shipmentRefund]);
    }

    /** @test */
    public function it_throws_an_exception_if_there_is_no_order_item_unit_with_given_id(): void
    {
        $unitRefund = new OrderItemUnitRefund(1, 500);

        $this->orderItemUnitRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->converter->convert([$unitRefund]);
    }

    /** @test */
    public function it_throws_an_exception_if_refund_amount_is_higher_than_order_item_unit_total(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $unitRefund = new OrderItemUnitRefund(1, 1001);

        $this->orderItemUnitRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($orderItemUnit);

        $orderItemUnit
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(500);

        $this->expectException(\InvalidArgumentException::class);

        $this->converter->convert([$unitRefund]);
    }
}

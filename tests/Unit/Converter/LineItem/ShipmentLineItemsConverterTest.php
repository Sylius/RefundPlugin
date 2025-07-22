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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Converter\LineItem\ShipmentLineItemsConverter;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\RefundPlugin\Factory\LineItemFactoryInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;

final class ShipmentLineItemsConverterTest extends TestCase
{
    private RepositoryInterface&MockObject $adjustmentRepository;

    private TaxRateProviderInterface&MockObject $taxRateProvider;

    private LineItemFactoryInterface&MockObject $lineItemFactory;

    private ShipmentLineItemsConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adjustmentRepository = $this->createMock(RepositoryInterface::class);
        $this->taxRateProvider = $this->createMock(TaxRateProviderInterface::class);
        $this->lineItemFactory = $this->createMock(LineItemFactoryInterface::class);

        $this->converter = new ShipmentLineItemsConverter(
            $this->adjustmentRepository,
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
    public function it_converts_shipment_unit_refunds_to_line_items(): void
    {
        $shippingAdjustment = $this->createMock(AdjustmentInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);
        $lineItem = $this->createMock(LineItemInterface::class);

        $shipmentRefund = new ShipmentRefund(1, 575);

        $this->adjustmentRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment);

        $shippingAdjustment
            ->expects(self::once())
            ->method('getLabel')
            ->willReturn('Galaxy post');

        $shippingAdjustment
            ->expects(self::once())
            ->method('getShipment')
            ->willReturn($shipment);

        $shipment
            ->expects($this->exactly(2))
            ->method('getAdjustmentsTotal')
            ->willReturn(1150);

        $shipment
            ->expects(self::once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        $taxAdjustment
            ->expects(self::once())
            ->method('getAmount')
            ->willReturn(150);

        $this->taxRateProvider
            ->expects(self::once())
            ->method('provide')
            ->with($shipment)
            ->willReturn('15%');

        $this->lineItemFactory
            ->expects(self::once())
            ->method('createWithData')
            ->with('Galaxy post', 1, 500, 575, 500, 575, 75, '15%')
            ->willReturn($lineItem);

        $result = $this->converter->convert([$shipmentRefund]);

        self::assertEquals([$lineItem], $result);
    }

    /** @test */
    public function it_throws_an_error_if_one_of_units_is_not_order_item_unit_refund(): void
    {
        $shipmentRefund = new ShipmentRefund(1, 575);
        $orderItemUnitRefund = new OrderItemUnitRefund(3, 300);

        $this->expectException(\InvalidArgumentException::class);

        $this->converter->convert([$shipmentRefund, $orderItemUnitRefund]);
    }

    /** @test */
    public function it_throws_an_exception_if_there_is_no_shipping_adjustment_with_given_id(): void
    {
        $shipmentRefund = new ShipmentRefund(1, 500);

        $this->adjustmentRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->converter->convert([$shipmentRefund]);
    }

    /** @test */
    public function it_throws_an_exception_if_refund_amount_is_higher_than_shipping_amount(): void
    {
        $shippingAdjustment = $this->createMock(AdjustmentInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);

        $shipmentRefund = new ShipmentRefund(1, 1001);

        $this->adjustmentRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment);

        $shippingAdjustment
            ->expects(self::once())
            ->method('getShipment')
            ->willReturn($shipment);

        $shipment
            ->expects(self::once())
            ->method('getAdjustmentsTotal')
            ->willReturn(1000);

        $this->expectException(\InvalidArgumentException::class);

        $this->converter->convert([$shipmentRefund]);
    }

    /** @test */
    public function it_throws_an_exception_if_shipment_has_more_tax_adjustments_than_one(): void
    {
        $shippingAdjustment = $this->createMock(AdjustmentInterface::class);
        $firstTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $secondTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);

        $shipmentRefund = new ShipmentRefund(1, 575);

        $this->adjustmentRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment);

        $shippingAdjustment
            ->expects($this->never())
            ->method('getLabel');

        $shippingAdjustment
            ->expects(self::once())
            ->method('getShipment')
            ->willReturn($shipment);

        $shipment
            ->expects(self::once())
            ->method('getAdjustmentsTotal')
            ->willReturn(1150);

        $shipment
            ->expects(self::once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstTaxAdjustment, $secondTaxAdjustment]));

        $this->taxRateProvider
            ->expects($this->never())
            ->method('provide');

        $this->expectException(MoreThanOneTaxAdjustment::class);

        $this->converter->convert([$shipmentRefund]);
    }
}

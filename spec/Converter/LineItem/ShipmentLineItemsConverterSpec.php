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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;

final class ShipmentLineItemsConverterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $adjustmentRepository, TaxRateProviderInterface $taxRateProvider): void
    {
        $this->beConstructedWith($adjustmentRepository, $taxRateProvider);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_converts_shipment_unit_refunds_to_line_items(
        RepositoryInterface $adjustmentRepository,
        TaxRateProviderInterface $taxRateProvider,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment,
        ShipmentInterface $shipment,
    ): void {
        $shipmentRefund = new ShipmentRefund(1, 575);

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;
        $shippingAdjustment->getLabel()->willReturn('Galaxy post');
        $shippingAdjustment->getShipment()->willReturn($shipment);

        $shipment->getAdjustmentsTotal()->willReturn(1150);
        $shipment
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getAmount()->willReturn(150);
        $taxRateProvider->provide($shipment)->willReturn('15%');

        $this->convert([$shipmentRefund])->shouldBeLike([new LineItem(
            'Galaxy post',
            1,
            500,
            575,
            500,
            575,
            75,
            '15%',
        )]);
    }

    function it_throws_an_error_if_one_of_units_is_not_order_item_unit_refund(): void
    {
        $shipmentRefund = new ShipmentRefund(1, 575);
        $orderItemUnitRefund = new OrderItemUnitRefund(3, 300);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$shipmentRefund, $orderItemUnitRefund]])
        ;
    }

    function it_throws_an_exception_if_there_is_no_shipping_adjustment_with_given_id(
        RepositoryInterface $adjustmentRepository,
    ): void {
        $shipmentRefund = new ShipmentRefund(1, 500);

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn(null)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$shipmentRefund]])
        ;
    }

    function it_throws_an_exception_if_refund_amount_is_higher_than_shipping_amount(
        RepositoryInterface $adjustmentRepository,
        AdjustmentInterface $shippingAdjustment,
        ShipmentInterface $shipment,
    ): void {
        $shipmentRefund = new ShipmentRefund(1, 1001);

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;

        $shippingAdjustment->getShipment()->willReturn($shipment);
        $shipment->getAdjustmentsTotal()->willReturn(1000);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$shipmentRefund]])
        ;
    }

    function it_throws_an_exception_if_shipment_has_more_tax_adjustments_than_one(
        RepositoryInterface $adjustmentRepository,
        TaxRateProviderInterface $taxRateProvider,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $firstTaxAdjustment,
        AdjustmentInterface $secondTaxAdjustment,
        ShipmentInterface $shipment,
    ): void {
        $shipmentRefund = new ShipmentRefund(1, 575);

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;
        $shippingAdjustment->getLabel()->willReturn('Galaxy post');
        $shippingAdjustment->getShipment()->willReturn($shipment);

        $shipment->getAdjustmentsTotal()->willReturn(1150);
        $shipment
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstTaxAdjustment->getWrappedObject(), $secondTaxAdjustment->getWrappedObject()]))
        ;

        $taxRateProvider->provide($shipment)->shouldNotBeCalled();

        $this
            ->shouldThrow(MoreThanOneTaxAdjustment::class)
            ->during('convert', [[$shipmentRefund]])
        ;
    }
}

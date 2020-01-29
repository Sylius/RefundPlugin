<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Converter\LineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;

final class ShipmentLineItemsConverterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $adjustmentRepository): void
    {
        $this->beConstructedWith($adjustmentRepository);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_converts_shipment_unit_refunds_to_line_items(
        RepositoryInterface $adjustmentRepository,
        AdjustmentInterface $shippingAdjustment
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 500);

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;

        $shippingAdjustment->getLabel()->willReturn('Galaxy post');
        $shippingAdjustment->getAmount()->willReturn(1000);

        $this->convert([$unitRefund])->shouldBeLike([new LineItem(
            'Galaxy post',
            1,
            500,
            500,
            500,
            500,
            0
        )]);
    }

    function it_throws_an_exception_if_there_is_no_shipping_adjustment_with_given_id(
        RepositoryInterface $adjustmentRepository
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 500);

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn(null)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$unitRefund]])
        ;
    }

    function it_throws_an_exception_if_refund_amount_is_higher_than_shipping_amount(
        RepositoryInterface $adjustmentRepository,
        AdjustmentInterface $shippingAdjustment
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 1001);

        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;

        $shippingAdjustment->getAmount()->willReturn(1000);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$unitRefund]])
        ;
    }
}

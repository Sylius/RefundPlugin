<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Converter\LineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;

final class LineItemsConverterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $orderItemUnitRepository): void
    {
        $this->beConstructedWith($orderItemUnitRepository);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_converts_unit_refunds_to_line_items(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $orderItemUnit,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 500);

        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);

        $orderItemUnit->getOrderItem()->willReturn($orderItem);
        $orderItemUnit->getTotal()->willReturn(1500);
        $orderItemUnit->getTaxTotal()->willReturn(300);
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$adjustment->getWrappedObject()]))
        ;

        $orderItem->getProductName()->willReturn('Portal gun');
        $adjustment->getLabel()->willReturn('25%');

        $this->convert([$unitRefund])->shouldBeLike(new ArrayCollection([new LineItem(
            'Portal gun',
            1,
            400,
            500,
            400,
            500,
            100,
            '25%'
        )]));
    }

    function it_groups_the_same_line_items_during_converting(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 500);
        $secondUnitRefund = new OrderItemUnitRefund(2, 960);
        $thirdUnitRefund = new OrderItemUnitRefund(2, 960);

        $orderItemUnitRepository->find(1)->willReturn($firstOrderItemUnit);

        $firstOrderItemUnit->getOrderItem()->willReturn($firstOrderItem);
        $firstOrderItemUnit->getTotal()->willReturn(1500);
        $firstOrderItemUnit->getTaxTotal()->willReturn(300);
        $firstOrderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstAdjustment->getWrappedObject()]))
        ;

        $firstOrderItem->getProductName()->willReturn('Portal gun');
        $firstAdjustment->getLabel()->willReturn('25%');

        $orderItemUnitRepository->find(2)->willReturn($secondOrderItemUnit);

        $secondOrderItemUnit->getOrderItem()->willReturn($secondOrderItem);
        $secondOrderItemUnit->getTotal()->willReturn(960);
        $secondOrderItemUnit->getTaxTotal()->willReturn(160);
        $secondOrderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$secondAdjustment->getWrappedObject()]))
        ;

        $secondOrderItem->getProductName()->willReturn('Space gun');
        $secondAdjustment->getLabel()->willReturn('20%');

        $this->convert([$firstUnitRefund, $secondUnitRefund, $thirdUnitRefund])->shouldBeLike(new ArrayCollection([
            new LineItem(
                'Portal gun',
                1,
                400,
                500,
                400,
                500,
                100,
                '25%'
            ),
            new LineItem(
                'Space gun',
                2,
                800,
                960,
                1600,
                1920,
                320,
                '20%'
            ),
        ]));
    }

    function it_throws_an_exception_if_there_is_no_shipping_adjustment_with_given_id(
        RepositoryInterface $orderItemUnitRepository
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 500);

        $orderItemUnitRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$unitRefund]])
        ;
    }

    function it_throws_an_exception_if_refund_amount_is_higher_than_shipping_amount(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $orderItemUnit
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 1001);

        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $orderItemUnit->getTotal()->willReturn(500);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$unitRefund]])
        ;
    }
}

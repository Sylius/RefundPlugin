<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\TaxItem;
use Sylius\RefundPlugin\Generator\TaxItemsGeneratorInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;

final class TaxItemsGeneratorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $orderItemUnitRepository): void
    {
        $this->beConstructedWith($orderItemUnitRepository);
    }

    function it_implements_tax_items_generator_interface(): void
    {
        $this->shouldImplement(TaxItemsGeneratorInterface::class);
    }

    function it_generates_tax_items(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 200);

        $orderItemUnitRepository->find(1)->willReturn($firstOrderItemUnit);
        $firstOrderItemUnit->getTotal()->willReturn(500);
        $firstOrderItemUnit->getTaxTotal()->willReturn(50);
        $firstOrderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstAdjustment->getWrappedObject()]))
        ;
        $firstAdjustment->getLabel()->willReturn('VAT');

        $orderItemUnitRepository->find(3)->willReturn($secondOrderItemUnit);
        $secondOrderItemUnit->getTotal()->willReturn(200);
        $secondOrderItemUnit->getTaxTotal()->willReturn(20);
        $secondOrderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$secondAdjustment->getWrappedObject()]))
        ;
        $secondAdjustment->getLabel()->willReturn('VAT');

        $this->generate([$firstUnitRefund, $secondUnitRefund])->shouldBeLike([new TaxItem('VAT', 70)]);
    }

    function it_generates_tax_items_with_partial_amount(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $adjustment
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 250);

        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $orderItemUnit->getTotal()->willReturn(500);
        $orderItemUnit->getTaxTotal()->willReturn(50);
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$adjustment->getWrappedObject()]))
        ;
        $adjustment->getLabel()->willReturn('VAT');

        $this->generate([$unitRefund])->shouldBeLike([new TaxItem('VAT', 25)]);
    }
}

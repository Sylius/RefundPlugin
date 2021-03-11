<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;

final class TaxRateProviderSpec extends ObjectBehavior
{
    function it_implements_tax_rate_provider_interface(): void
    {
        $this->shouldImplement(TaxRateProviderInterface::class);
    }

    function it_provides_a_tax_rate_from_tax_adjustment_details(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $taxAdjustment
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getDetails()->willReturn(['taxRateAmount' => 0.2]);

        $this->provide($orderItemUnit)->shouldReturn('20%');
    }

    function it_returns_null_if_there_is_no_tax_adjustment(OrderItemUnitInterface $orderItemUnit): void
    {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]))
        ;

        $this->provide($orderItemUnit)->shouldReturn(null);
    }

    function it_returns_null_if_there_is_no_adjustment_with_details_with_tax_rate_amount(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $taxAdjustment

    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getDetails()->willReturn([]);

        $this->provide($orderItemUnit)->shouldReturn(null);
    }

    function it_throws_exception_if_order_item_unit_has_more_than_1_tax_adjustment(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $firstTaxAdjustment,
        AdjustmentInterface $secondTaxAdjustment

    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstTaxAdjustment->getWrappedObject(), $secondTaxAdjustment->getWrappedObject()]))
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$orderItemUnit])
        ;
    }
}

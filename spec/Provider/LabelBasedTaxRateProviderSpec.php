<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;

final class LabelBasedTaxRateProviderSpec extends ObjectBehavior
{
    function it_implements_tax_rate_provider_interface(): void
    {
        $this->shouldImplement(TaxRateProviderInterface::class);
    }

    function it_provides_a_tax_rate_from_tax_adjustment_label(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $taxAdjustment
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getLabel()->willReturn('VAT (20%)');

        $this->provide($orderItemUnit)->shouldReturn('20%');
    }

    function it_provides_a_tax_adjustment_label_if_the_value_does_not_match_the_pattern(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $taxAdjustment
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getLabel()->willReturn('20%');

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
}

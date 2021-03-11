<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Provider\TaxRateAmountProviderInterface;


final class TaxRateAmountProviderSpec extends ObjectBehavior
{
    function it_implements_tax_rate_provider_interface(): void
    {
        $this->shouldImplement(TaxRateAmountProviderInterface::class);
    }

    function it_provides_a_tax_rate_amount_from_tax_adjustment(AdjustmentInterface $adjustment): void
    {
        $adjustment->getDetails()->willReturn(['taxRateAmount' => 0.20]);

        $this->provide($adjustment)->shouldReturn(0.20);
    }


    function it_throws_exception_if_order_item_unit_has_more_than_1_tax_adjustment(AdjustmentInterface $adjustment): void
    {
        $adjustment->getDetails()->willReturn(['detail' => 'detail']);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$adjustment])
        ;
    }
}

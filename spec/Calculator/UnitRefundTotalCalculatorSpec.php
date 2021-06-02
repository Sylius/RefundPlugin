<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class UnitRefundTotalCalculatorSpec extends ObjectBehavior
{
    public function let(RemainingTotalProviderInterface $remainingTotalProvider): void
    {
        $this->beConstructedWith($remainingTotalProvider);
    }

    public function it_implements_unit_refund_total_calculator_interface(): void
    {
        $this->shouldImplement(UnitRefundTotalCalculatorInterface::class);
    }

    public function it_provides_remaining_total_if_full_refund_option_is_chosen(
        RemainingTotalProviderInterface $remainingTotalProvider
    ): void {
        $refundType = RefundType::shipment();

        $remainingTotalProvider->getTotalLeftToRefund(100, $refundType)->willReturn(100);

        $this
            ->calculateForUnitWithIdAndType(100, $refundType)
            ->shouldReturn(100)
        ;
    }

    public function it_provides_specified_amount_as_an_integer(): void
    {
        $this
            ->calculateForUnitWithIdAndType(100, RefundType::shipment(), 30.40)
            ->shouldReturn(3040)
        ;
    }
}

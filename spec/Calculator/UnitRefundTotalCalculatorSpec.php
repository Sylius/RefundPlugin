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

namespace spec\Sylius\RefundPlugin\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class UnitRefundTotalCalculatorSpec extends ObjectBehavior
{
    function let(RemainingTotalProviderInterface $remainingTotalProvider): void
    {
        $this->beConstructedWith($remainingTotalProvider);
    }

    function it_implements_unit_refund_total_calculator_interface(): void
    {
        $this->shouldImplement(UnitRefundTotalCalculatorInterface::class);
    }

    function it_provides_remaining_total_if_full_refund_option_is_chosen(
        RemainingTotalProviderInterface $remainingTotalProvider,
    ): void {
        $refundType = RefundType::shipment();

        $remainingTotalProvider->getTotalLeftToRefund(100, $refundType)->willReturn(100);

        $this
            ->calculateForUnitWithIdAndType(100, $refundType)
            ->shouldReturn(100)
        ;
    }

    function it_provides_specified_amount_as_an_integer(): void
    {
        $this
            ->calculateForUnitWithIdAndType(100, RefundType::shipment(), 30.40)
            ->shouldReturn(3040)
        ;
    }
}

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

namespace spec\Sylius\RefundPlugin\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundUnitsConverterSpec extends ObjectBehavior
{
    function let(UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator): void
    {
        $this->beConstructedWith($unitRefundTotalCalculator);
    }

    function it_implements_refund_units_converter_interface(): void
    {
        $this->shouldImplement(RefundUnitsConverterInterface::class);
    }

    function it_converts_refund_units_from_request_with_full_prices_to_models(
        UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator,
    ): void {
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(1, RefundType::orderItemUnit(), null)->willReturn(1000);
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(2, RefundType::orderItemUnit(), null)->willReturn(3000);

        $this
            ->convert(
                [
                    1 => ['full' => 'on'],
                    2 => ['full' => 'on'],
                ],
                OrderItemUnitRefund::class,
            )
            ->shouldBeLike([new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)])
        ;
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_converts_refund_units_from_request_with_full_prices_to_models_with_deprecations(
        UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator,
    ): void {
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(1, RefundType::orderItemUnit(), null)->willReturn(1000);
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(2, RefundType::orderItemUnit(), null)->willReturn(3000);

        $this
            ->convert(
                [
                    1 => ['full' => 'on'],
                    2 => ['full' => 'on'],
                ],
                RefundType::orderItemUnit(),
                OrderItemUnitRefund::class,
            )
            ->shouldBeLike([new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)])
        ;
    }

    function it_converts_refund_units_from_request_with_partial_prices_to_models(
        UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator,
    ): void {
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(1, RefundType::orderItemUnit(), 10.00)->willReturn(1000);
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(2, RefundType::orderItemUnit(), null)->willReturn(3000);

        $this
            ->convert(
                [
                    1 => ['amount' => '10.00'],
                    2 => ['full' => 'on'],
                ],
                OrderItemUnitRefund::class,
            )
            ->shouldBeLike([new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)])
        ;
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_converts_refund_units_from_request_with_partial_prices_to_models_with_deprecations(
        UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator,
    ): void {
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(1, RefundType::orderItemUnit(), 10.00)->willReturn(1000);
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(2, RefundType::orderItemUnit(), null)->willReturn(3000);

        $this
            ->convert(
                [
                    1 => ['amount' => '10.00'],
                    2 => ['full' => 'on'],
                ],
                RefundType::orderItemUnit(),
                OrderItemUnitRefund::class,
            )
            ->shouldBeLike([new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)])
        ;
    }
}

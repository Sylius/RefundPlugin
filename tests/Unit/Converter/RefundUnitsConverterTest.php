<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit\Converter;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Converter\RefundUnitsConverter;
use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundUnitsConverterTest extends TestCase
{
    private UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator;
    private RefundUnitsConverter $converter;

    protected function setUp(): void
    {
        $this->unitRefundTotalCalculator = $this->createMock(UnitRefundTotalCalculatorInterface::class);
        $this->converter = new RefundUnitsConverter($this->unitRefundTotalCalculator);
    }

    /** @test */
    function it_implements_refund_units_converter_interface(): void
    {
        $this->assertInstanceOf(RefundUnitsConverterInterface::class, $this->converter);
    }

    /** @test */
    function it_converts_refund_units_from_request_with_full_prices_to_models(): void
    {
        $this->unitRefundTotalCalculator
            ->expects($this->exactly(2))
            ->method('calculateForUnitWithIdAndType')
            ->willReturnCallback(function (int $id, RefundType $type, ?float $amount) {
                return match ($id) {
                    1 => 1000,
                    2 => 3000,
                };
            });

        $result = $this->converter->convert(
            [
                1 => ['full' => 'on'],
                2 => ['full' => 'on'],
            ],
            OrderItemUnitRefund::class,
        );

        $this->assertEquals([new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)], $result);
    }

    /** @test */
    function it_converts_refund_units_from_request_with_partial_prices_to_models(): void
    {
        $this->unitRefundTotalCalculator
            ->expects($this->exactly(2))
            ->method('calculateForUnitWithIdAndType')
            ->willReturnCallback(function (int $id, RefundType $type, ?float $amount) {
                return match ($id) {
                    1 => 1000,
                    2 => 3000,
                };
            });

        $result = $this->converter->convert(
            [
                1 => ['amount' => '10.00'],
                2 => ['full' => 'on'],
            ],
            OrderItemUnitRefund::class,
        );

        $this->assertEquals([new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)], $result);
    }
}
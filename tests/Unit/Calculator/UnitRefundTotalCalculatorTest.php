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

namespace Tests\Sylius\RefundPlugin\Unit\Calculator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculator;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class UnitRefundTotalCalculatorTest extends TestCase
{
    private RemainingTotalProviderInterface&MockObject $remainingTotalProvider;

    private UnitRefundTotalCalculator $unitRefundTotalCalculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->remainingTotalProvider = $this->createMock(RemainingTotalProviderInterface::class);
        $this->unitRefundTotalCalculator = new UnitRefundTotalCalculator($this->remainingTotalProvider);
    }

    /** @test */
    public function it_implements_unit_refund_total_calculator_interface(): void
    {
        self::assertInstanceOf(UnitRefundTotalCalculatorInterface::class, $this->unitRefundTotalCalculator);
    }

    /** @test */
    public function it_provides_remaining_total_if_full_refund_option_is_chosen(): void
    {
        $refundType = RefundType::shipment();

        $this->remainingTotalProvider
            ->expects(self::once())
            ->method('getTotalLeftToRefund')
            ->with(100, $refundType)
            ->willReturn(100);

        $result = $this->unitRefundTotalCalculator->calculateForUnitWithIdAndType(100, $refundType);

        self::assertEquals(100, $result);
    }

    /** @test */
    public function it_provides_specified_amount_as_an_integer(): void
    {
        $result = $this->unitRefundTotalCalculator->calculateForUnitWithIdAndType(100, RefundType::shipment(), 30.40);

        self::assertEquals(3040, $result);
    }
}

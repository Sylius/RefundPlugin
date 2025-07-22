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

namespace Tests\Sylius\RefundPlugin\Unit\Checker;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityChecker;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class UnitRefundingAvailabilityCheckerTest extends TestCase
{
    private RemainingTotalProviderInterface $remainingTotalProvider;
    private UnitRefundingAvailabilityChecker $checker;

    protected function setUp(): void
    {
        $this->remainingTotalProvider = $this->createMock(RemainingTotalProviderInterface::class);
        $this->checker = new UnitRefundingAvailabilityChecker($this->remainingTotalProvider);
    }

    /** @test */
    function it_implements_unit_refunding_availability_checker_interface(): void
    {
        $this->assertInstanceOf(UnitRefundingAvailabilityCheckerInterface::class, $this->checker);
    }

    /** @test */
    function it_returns_false_if_remaining_unit_total_is_0(): void
    {
        $type = RefundType::orderItemUnit();

        $this->remainingTotalProvider
            ->expects($this->once())
            ->method('getTotalLeftToRefund')
            ->with(1, $type)
            ->willReturn(0);

        $result = $this->checker->__invoke(1, $type);

        $this->assertFalse($result);
    }

    /** @test */
    function it_returns_true_if_remaining_unit_total_is_more_than_0(): void
    {
        $type = RefundType::shipment();

        $this->remainingTotalProvider
            ->expects($this->once())
            ->method('getTotalLeftToRefund')
            ->with(1, $type)
            ->willReturn(100);

        $result = $this->checker->__invoke(1, $type);

        $this->assertTrue($result);
    }
}
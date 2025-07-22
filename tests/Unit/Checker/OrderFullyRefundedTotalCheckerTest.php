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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalChecker;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderFullyRefundedTotalCheckerTest extends TestCase
{
    private OrderRefundedTotalProviderInterface&MockObject $orderRefundedTotalProvider;

    private OrderFullyRefundedTotalChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRefundedTotalProvider = $this->createMock(OrderRefundedTotalProviderInterface::class);
        $this->checker = new OrderFullyRefundedTotalChecker($this->orderRefundedTotalProvider);
    }

    /** @test */
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(OrderFullyRefundedTotalChecker::class, $this->checker);
    }

    /** @test */
    public function it_implements_order_fully_refunded_total_checker_interface(): void
    {
        self::assertInstanceOf(OrderFullyRefundedTotalCheckerInterface::class, $this->checker);
    }

    /** @test */
    public function it_returns_false_if_order_refunded_total_is_lower_than_order_total(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(1000);

        $this->orderRefundedTotalProvider
            ->expects(self::once())
            ->method('__invoke')
            ->with($order)
            ->willReturn(500);

        $result = $this->checker->isOrderFullyRefunded($order);

        self::assertFalse($result);
    }

    /** @test */
    public function it_returns_true_if_order_refunded_total_is_equal_to_order_total(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(1000);

        $this->orderRefundedTotalProvider
            ->expects(self::once())
            ->method('__invoke')
            ->with($order)
            ->willReturn(1000);

        $result = $this->checker->isOrderFullyRefunded($order);

        self::assertTrue($result);
    }
}

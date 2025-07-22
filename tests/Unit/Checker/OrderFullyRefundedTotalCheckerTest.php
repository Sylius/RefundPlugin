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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalChecker;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderFullyRefundedTotalCheckerTest extends TestCase
{
    private OrderRefundedTotalProviderInterface $orderRefundedTotalProvider;
    private OrderFullyRefundedTotalChecker $checker;

    protected function setUp(): void
    {
        $this->orderRefundedTotalProvider = $this->createMock(OrderRefundedTotalProviderInterface::class);
        $this->checker = new OrderFullyRefundedTotalChecker($this->orderRefundedTotalProvider);
    }

    /** @test */
    function it_is_initializable(): void
    {
        $this->assertInstanceOf(OrderFullyRefundedTotalChecker::class, $this->checker);
    }

    /** @test */
    function it_implements_order_fully_refunded_total_checker_interface(): void
    {
        $this->assertInstanceOf(OrderFullyRefundedTotalCheckerInterface::class, $this->checker);
    }

    /** @test */
    function it_returns_false_if_order_refunded_total_is_lower_than_order_total(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order
            ->expects($this->once())
            ->method('getTotal')
            ->willReturn(1000);

        $this->orderRefundedTotalProvider
            ->expects($this->once())
            ->method('__invoke')
            ->with($order)
            ->willReturn(500);

        $result = $this->checker->isOrderFullyRefunded($order);

        $this->assertFalse($result);
    }

    /** @test */
    function it_returns_true_if_order_refunded_total_is_equal_to_order_total(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order
            ->expects($this->once())
            ->method('getTotal')
            ->willReturn(1000);

        $this->orderRefundedTotalProvider
            ->expects($this->once())
            ->method('__invoke')
            ->with($order)
            ->willReturn(1000);

        $result = $this->checker->isOrderFullyRefunded($order);

        $this->assertTrue($result);
    }
}
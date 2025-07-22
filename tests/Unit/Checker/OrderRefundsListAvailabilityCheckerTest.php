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
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundsListAvailabilityChecker;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;

final class OrderRefundsListAvailabilityCheckerTest extends TestCase
{
    private OrderRepositoryInterface&MockObject $orderRepository;
    private OrderRefundsListAvailabilityChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->checker = new OrderRefundsListAvailabilityChecker($this->orderRepository);
    }

    /** @test */
    function it_implements_order_refunding_availability_checker_interface(): void
    {
        self::assertInstanceOf(OrderRefundingAvailabilityCheckerInterface::class, $this->checker);
    }

    /** @test */
    function it_returns_true_if_order_is_paid_and_not_free(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_PAID);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(100);

        $result = $this->checker->__invoke('00000007');

        self::assertTrue($result);
    }

    /** @test */
    function it_returns_true_if_order_is_refunded_and_not_free(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_REFUNDED);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(100);

        $result = $this->checker->__invoke('00000007');

        self::assertTrue($result);
    }

    /** @test */
    function it_returns_true_if_order_is_partially_refunded_and_not_free(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_PARTIALLY_REFUNDED);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(100);

        $result = $this->checker->__invoke('00000007');

        self::assertTrue($result);
    }

    /** @test */
    function it_returns_false_if_order_is_in_other_state_and_not_free(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);

        // getTotal() is not called due to short-circuit evaluation

        $result = $this->checker->__invoke('00000007');

        self::assertFalse($result);
    }

    /** @test */
    function it_returns_false_if_order_is_paid_and_free(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_PAID);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(0);

        $result = $this->checker->__invoke('00000007');

        self::assertFalse($result);
    }

    /** @test */
    function it_returns_false_if_order_is_refunded_and_free(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_REFUNDED);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(0);

        $result = $this->checker->__invoke('00000007');

        self::assertFalse($result);
    }

    /** @test */
    function it_returns_false_if_order_is_in_other_state_and_free(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);

        // getTotal() is not called due to short-circuit evaluation

        $result = $this->checker->__invoke('00000007');

        self::assertFalse($result);
    }

    /** @test */
    function it_returns_true_if_refunding_checker_allows_it(): void
    {
        $orderRefundingAvailabilityChecker = $this->createMock(OrderRefundingAvailabilityCheckerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $checker = new OrderRefundsListAvailabilityChecker($this->orderRepository, $orderRefundingAvailabilityChecker);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $orderRefundingAvailabilityChecker
            ->expects(self::once())
            ->method('__invoke')
            ->with('00000007')
            ->willReturn(true);

        $result = $checker->__invoke('00000007');

        self::assertTrue($result);
    }

    /** @test */
    function it_returns_true_if_checker_returns_false_but_order_is_refunded(): void
    {
        $orderRefundingAvailabilityChecker = $this->createMock(OrderRefundingAvailabilityCheckerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $checker = new OrderRefundsListAvailabilityChecker($this->orderRepository, $orderRefundingAvailabilityChecker);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $orderRefundingAvailabilityChecker
            ->expects(self::once())
            ->method('__invoke')
            ->with('00000007')
            ->willReturn(false);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_REFUNDED);

        $result = $checker->__invoke('00000007');

        self::assertTrue($result);
    }

    /** @test */
    function it_returns_false_if_checker_returns_false_and_order_is_not_refunded(): void
    {
        $orderRefundingAvailabilityChecker = $this->createMock(OrderRefundingAvailabilityCheckerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $checker = new OrderRefundsListAvailabilityChecker($this->orderRepository, $orderRefundingAvailabilityChecker);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $orderRefundingAvailabilityChecker
            ->expects(self::once())
            ->method('__invoke')
            ->with('00000007')
            ->willReturn(false);

        $order
            ->expects(self::once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_PAID);

        $result = $checker->__invoke('00000007');

        self::assertFalse($result);
    }
}

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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityChecker;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;

final class OrderRefundingAvailabilityCheckerTest extends TestCase
{
    /** @var OrderRepositoryInterface<OrderInterface>&MockObject */
    private OrderRepositoryInterface&MockObject $orderRepository;

    private OrderRefundingAvailabilityChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->checker = new OrderRefundingAvailabilityChecker($this->orderRepository);
    }

    #[Test]
    public function it_implements_order_refunding_availability_checker_interface(): void
    {
        self::assertInstanceOf(OrderRefundingAvailabilityCheckerInterface::class, $this->checker);
    }

    #[Test]
    public function it_returns_true_if_order_is_paid_and_not_free(): void
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

    #[Test]
    public function it_returns_true_if_order_is_partially_refunded_and_not_free(): void
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

    #[Test]
    public function it_returns_false_if_order_is_in_other_state_and_not_free(): void
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

    #[Test]
    public function it_returns_false_if_order_is_free(): void
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

    #[Test]
    public function it_returns_false_if_order_is_partially_refunded_and_free(): void
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
            ->willReturn(0);

        $result = $this->checker->__invoke('00000007');

        self::assertFalse($result);
    }

    #[Test]
    public function it_returns_false_if_order_is_in_other_state_and_free(): void
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

    #[Test]
    public function it_returns_true_if_partially_refund_transition_is_possible_and_total_is_not_zero(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $checker = new OrderRefundingAvailabilityChecker($this->orderRepository, $stateMachine);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $stateMachine
            ->expects(self::once())
            ->method('can')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)
            ->willReturn(true);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(1000);

        $result = $checker->__invoke('00000007');

        self::assertTrue($result);
    }

    #[Test]
    public function it_returns_true_if_refund_transition_is_possible_and_total_is_not_zero(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $checker = new OrderRefundingAvailabilityChecker($this->orderRepository, $stateMachine);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $stateMachine
            ->expects($this->exactly(2))
            ->method('can')
            ->willReturnCallback(function ($order, $graph, $transition) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    $this->assertEquals($order, $order);
                    $this->assertEquals(OrderPaymentTransitions::GRAPH, $graph);
                    $this->assertEquals(OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND, $transition);

                    return false;
                }

                if ($callCount === 2) {
                    $this->assertEquals($order, $order);
                    $this->assertEquals(OrderPaymentTransitions::GRAPH, $graph);
                    $this->assertEquals(OrderPaymentTransitions::TRANSITION_REFUND, $transition);

                    return true;
                }

                return false;
            });

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(1000);

        $result = $checker->__invoke('00000007');

        self::assertTrue($result);
    }

    #[Test]
    public function it_returns_false_if_no_transition_is_possible_even_if_total_is_not_zero(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $checker = new OrderRefundingAvailabilityChecker($this->orderRepository, $stateMachine);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $stateMachine
            ->expects($this->exactly(2))
            ->method('can')
            ->willReturnCallback(function ($order, $graph, $transition) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    $this->assertEquals($order, $order);
                    $this->assertEquals(OrderPaymentTransitions::GRAPH, $graph);
                    $this->assertEquals(OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND, $transition);

                    return false;
                }

                if ($callCount === 2) {
                    $this->assertEquals($order, $order);
                    $this->assertEquals(OrderPaymentTransitions::GRAPH, $graph);
                    $this->assertEquals(OrderPaymentTransitions::TRANSITION_REFUND, $transition);

                    return false;
                }

                return false;
            });

        // getTotal() is not called due to short-circuit evaluation

        $result = $checker->__invoke('00000007');

        self::assertFalse($result);
    }

    #[Test]
    public function it_returns_false_if_transition_is_possible_but_total_is_zero(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $checker = new OrderRefundingAvailabilityChecker($this->orderRepository, $stateMachine);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('00000007')
            ->willReturn($order);

        $stateMachine
            ->expects(self::once())
            ->method('can')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)
            ->willReturn(true);

        $order
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(0);

        $result = $checker->__invoke('00000007');

        self::assertFalse($result);
    }
}

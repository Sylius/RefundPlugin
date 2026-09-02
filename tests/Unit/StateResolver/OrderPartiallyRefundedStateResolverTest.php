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

namespace Tests\Sylius\RefundPlugin\Unit\StateResolver;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolver;

final class OrderPartiallyRefundedStateResolverTest extends TestCase
{
    /** @var OrderRepositoryInterface<OrderInterface>&MockObject */
    private OrderRepositoryInterface&MockObject $orderRepository;

    private StateMachineInterface&MockObject $stateMachineFactory;

    private EntityManagerInterface&MockObject $orderManager;

    private OrderPartiallyRefundedStateResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->stateMachineFactory = $this->createMock(StateMachineInterface::class);
        $this->orderManager = $this->createMock(EntityManagerInterface::class);

        $this->resolver = new OrderPartiallyRefundedStateResolver($this->orderRepository, $this->stateMachineFactory, $this->orderManager);
    }

    #[Test]
    public function it_marks_order_as_partially_refunded(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $orderManager = $this->createMock(EntityManagerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $resolver = new OrderPartiallyRefundedStateResolver($orderRepository, $stateMachine, $orderManager);

        $orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('000777')
            ->willReturn($order);

        $stateMachine
            ->expects(self::once())
            ->method('can')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)
            ->willReturn(true);

        $stateMachine
            ->expects(self::once())
            ->method('apply')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND);

        $orderManager
            ->expects(self::once())
            ->method('flush');

        $resolver->resolve('000777');
    }

    #[Test]
    public function it_marks_order_as_partially_refunded_again_if_it_is_already_partially_refunded(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $orderManager = $this->createMock(EntityManagerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $resolver = new OrderPartiallyRefundedStateResolver($orderRepository, $stateMachine, $orderManager);

        $orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('000777')
            ->willReturn($order);

        $stateMachine
            ->expects(self::once())
            ->method('can')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)
            ->willReturn(true);

        $stateMachine
            ->expects(self::once())
            ->method('apply')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND);

        $orderManager
            ->expects(self::once())
            ->method('flush');

        $resolver->resolve('000777');
    }

    #[Test]
    public function it_does_nothing_if_partially_refund_transition_is_not_applicable(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $orderManager = $this->createMock(EntityManagerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $resolver = new OrderPartiallyRefundedStateResolver($orderRepository, $stateMachine, $orderManager);

        $orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('000777')
            ->willReturn($order);

        $stateMachine
            ->expects(self::once())
            ->method('can')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)
            ->willReturn(false);

        $stateMachine
            ->expects($this->never())
            ->method('apply');

        $orderManager
            ->expects($this->never())
            ->method('flush');

        $resolver->resolve('000777');
    }

    #[Test]
    public function it_throws_exception_if_there_is_no_order_with_given_number(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $orderManager = $this->createMock(EntityManagerInterface::class);

        $resolver = new OrderPartiallyRefundedStateResolver($orderRepository, $stateMachine, $orderManager);

        $orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('000777')
            ->willReturn(null);

        $this->expectException(OrderNotFound::class);
        $this->expectExceptionMessage('Order with number "000777" has not been found');

        $resolver->resolve('000777');
    }

    #[Test]
    public function it_uses_winzou_state_machine_if_abstraction_not_passed_to_mark_order_as_partially_refunded(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('000777')
            ->willReturn($order);

        $this->stateMachineFactory
            ->expects(self::once())
            ->method('can')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)
            ->willReturn(true);

        $this->stateMachineFactory
            ->expects(self::once())
            ->method('apply')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND);

        $this->orderManager
            ->expects(self::once())
            ->method('flush');

        $this->resolver->resolve('000777');
    }
}

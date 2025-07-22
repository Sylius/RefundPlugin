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
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolver;

final class OrderFullyRefundedStateResolverTest extends TestCase
{
    private StateMachineInterface $stateMachineFactory;
    private EntityManagerInterface $orderManager;
    private OrderFullyRefundedTotalCheckerInterface $orderFullyRefundedTotalChecker;
    private OrderRepositoryInterface $orderRepository;
    private OrderFullyRefundedStateResolver $resolver;

    protected function setUp(): void
    {
        $this->stateMachineFactory = $this->createMock(StateMachineInterface::class);
        $this->orderManager = $this->createMock(EntityManagerInterface::class);
        $this->orderFullyRefundedTotalChecker = $this->createMock(OrderFullyRefundedTotalCheckerInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        
        $this->resolver = new OrderFullyRefundedStateResolver(
            $this->stateMachineFactory,
            $this->orderManager,
            $this->orderFullyRefundedTotalChecker,
            $this->orderRepository
        );
    }

    /** @test */
    function it_applies_refund_transition_on_order(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $orderManager = $this->createMock(EntityManagerInterface::class);
        $orderFullyRefundedTotalChecker = $this->createMock(OrderFullyRefundedTotalCheckerInterface::class);
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $resolver = new OrderFullyRefundedStateResolver($stateMachine, $orderManager, $orderFullyRefundedTotalChecker, $orderRepository);

        $orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn($order);

        $orderFullyRefundedTotalChecker
            ->expects($this->once())
            ->method('isOrderFullyRefunded')
            ->with($order)
            ->willReturn(true);

        $order
            ->expects($this->once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_PAID);

        $stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND);

        $orderManager
            ->expects($this->once())
            ->method('flush');

        $resolver->resolve('000222');
    }

    /** @test */
    function it_does_nothing_if_order_state_is_fully_refunded(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $orderManager = $this->createMock(EntityManagerInterface::class);
        $orderFullyRefundedTotalChecker = $this->createMock(OrderFullyRefundedTotalCheckerInterface::class);
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $resolver = new OrderFullyRefundedStateResolver($stateMachine, $orderManager, $orderFullyRefundedTotalChecker, $orderRepository);

        $orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn($order);

        $orderFullyRefundedTotalChecker
            ->expects($this->once())
            ->method('isOrderFullyRefunded')
            ->with($order)
            ->willReturn(true);

        $order
            ->expects($this->once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_REFUNDED);

        $stateMachine
            ->expects($this->never())
            ->method('apply');

        $resolver->resolve('000222');
    }

    /** @test */
    function it_does_nothing_if_order_is_not_fully_refunded(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $orderManager = $this->createMock(EntityManagerInterface::class);
        $orderFullyRefundedTotalChecker = $this->createMock(OrderFullyRefundedTotalCheckerInterface::class);
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $resolver = new OrderFullyRefundedStateResolver($stateMachine, $orderManager, $orderFullyRefundedTotalChecker, $orderRepository);

        $orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn($order);

        $orderFullyRefundedTotalChecker
            ->expects($this->once())
            ->method('isOrderFullyRefunded')
            ->with($order)
            ->willReturn(false);

        $stateMachine
            ->expects($this->never())
            ->method('apply');

        $resolver->resolve('000222');
    }

    /** @test */
    function it_throws_an_exception_if_there_is_no_order_with_given_number(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $orderManager = $this->createMock(EntityManagerInterface::class);
        $orderFullyRefundedTotalChecker = $this->createMock(OrderFullyRefundedTotalCheckerInterface::class);
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);

        $resolver = new OrderFullyRefundedStateResolver($stateMachine, $orderManager, $orderFullyRefundedTotalChecker, $orderRepository);

        $orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $resolver->resolve('000222');
    }

    /** @test */
    function it_uses_winzou_state_machine_if_abstraction_not_passed_to_apply_refund_transition_on_order(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn($order);

        $this->orderFullyRefundedTotalChecker
            ->expects($this->once())
            ->method('isOrderFullyRefunded')
            ->with($order)
            ->willReturn(true);

        $order
            ->expects($this->once())
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_PAID);

        $this->stateMachineFactory
            ->expects($this->once())
            ->method('apply')
            ->with($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND);

        $this->orderManager
            ->expects($this->once())
            ->method('flush');

        $this->resolver->resolve('000222');
    }
}
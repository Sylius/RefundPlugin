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

namespace spec\Sylius\RefundPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;

final class OrderRefundingAvailabilityCheckerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_implements_order_refunding_availability_checker_interface(): void
    {
        $this->shouldImplement(OrderRefundingAvailabilityCheckerInterface::class);
    }

    function it_returns_true_if_order_is_paid_and_not_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);
        $order->getTotal()->willReturn(100);

        $this('00000007')->shouldReturn(true);
    }

    function it_returns_true_if_order_is_partially_refunded_and_not_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PARTIALLY_REFUNDED);
        $order->getTotal()->willReturn(100);

        $this('00000007')->shouldReturn(true);
    }

    function it_returns_false_if_order_is_in_other_state_and_not_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $order->getTotal()->willReturn(100);

        $this('00000007')->shouldReturn(false);
    }

    function it_returns_false_if_order_is_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);
        $order->getTotal()->willReturn(0);

        $this('00000007')->shouldReturn(false);
    }

    function it_returns_false_if_order_is_partially_refunded_and_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PARTIALLY_REFUNDED);
        $order->getTotal()->willReturn(0);

        $this('00000007')->shouldReturn(false);
    }

    function it_returns_false_if_order_is_in_other_state_and_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $order->getTotal()->willReturn(0);

        $this('00000007')->shouldReturn(false);
    }

    function it_returns_true_if_partially_refund_transition_is_possible_and_total_is_not_zero(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachine);

        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)->willReturn(true);
        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)->willReturn(false);
        $order->getTotal()->willReturn(1000);

        $this->__invoke('00000007')->shouldReturn(true);
    }

    function it_returns_true_if_refund_transition_is_possible_and_total_is_not_zero(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachine);

        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)->willReturn(false);
        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)->willReturn(true);
        $order->getTotal()->willReturn(1000);

        $this->__invoke('00000007')->shouldReturn(true);
    }

    function it_returns_false_if_no_transition_is_possible_even_if_total_is_not_zero(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachine);

        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)->willReturn(false);
        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)->willReturn(false);
        $order->getTotal()->willReturn(1000);

        $this->__invoke('00000007')->shouldReturn(false);
    }

    function it_returns_false_if_transition_is_possible_but_total_is_zero(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachine);

        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)->willReturn(true);
        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)->willReturn(true);
        $order->getTotal()->willReturn(0);

        $this->__invoke('00000007')->shouldReturn(false);
    }
}

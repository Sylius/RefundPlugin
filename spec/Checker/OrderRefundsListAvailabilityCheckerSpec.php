<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;

final class OrderRefundsListAvailabilityCheckerSpec extends ObjectBehavior
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

    function it_returns_true_if_order_is_refunded_and_not_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_REFUNDED);
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

    function it_returns_false_if_order_is_paid_and_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);
        $order->getTotal()->willReturn(0);

        $this('00000007')->shouldReturn(false);
    }

    function it_returns_false_if_order_is_refunded_and_free(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_REFUNDED);
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
}

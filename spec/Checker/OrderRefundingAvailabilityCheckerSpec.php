<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
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

    function it_returns_true_if_order_is_paid(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $this->__invoke('00000007')->shouldReturn(true);
    }

    function it_returns_true_if_order_is_unpaid(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): void {
        $orderRepository->findOneByNumber('00000007')->willReturn($order);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);

        $this->__invoke('00000007')->shouldReturn(false);
    }
}

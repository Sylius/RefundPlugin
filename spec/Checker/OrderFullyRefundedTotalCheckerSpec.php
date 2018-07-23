<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalChecker;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderFullyRefundedTotalCheckerSpec extends ObjectBehavior
{
    function let(OrderRefundedTotalProviderInterface $orderRefundedTotalProvider): void
    {
        $this->beConstructedWith($orderRefundedTotalProvider);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderFullyRefundedTotalChecker::class);
    }

    function it_implements_order_fully_refunded_total_checker_interface(): void
    {
        $this->shouldImplement(OrderFullyRefundedTotalCheckerInterface::class);
    }

    function it_returns_false_if_order_total_has_not_been_fully_refunded(
        OrderInterface $order,
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider
    ): void {
        $order->getTotal()->willReturn(1000);
        $order->getNumber()->willReturn('0000001');

        $orderRefundedTotalProvider->__invoke('0000001')->willReturn(500);

        $this->check($order, 200)->shouldReturn(false);
    }

    function it_returns_true_if_order_total_has_been_fully_refunded_with_current_refund_request(
        OrderInterface $order,
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider
    ): void {
        $order->getTotal()->willReturn(1000);
        $order->getNumber()->willReturn('0000001');

        $orderRefundedTotalProvider->__invoke('0000001')->willReturn(0);

        $this->check($order, 1000)->shouldReturn(true);
    }

    function it_returns_true_if_order_total_has_been_fully_refunded_with_current_and_previous_refund_requests(
        OrderInterface $order,
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider
    ): void {
        $order->getTotal()->willReturn(1000);
        $order->getNumber()->willReturn('0000001');

        $orderRefundedTotalProvider->__invoke('0000001')->willReturn(500);

        $this->check($order, 500)->shouldReturn(true);
    }
}

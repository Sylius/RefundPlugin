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

    function it_returns_false_if_order_refunded_total_is_lower_than_order_total(
        OrderInterface $order,
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider,
    ): void {
        $order->getTotal()->willReturn(1000);

        $orderRefundedTotalProvider->__invoke($order)->willReturn(500);

        $this->isOrderFullyRefunded($order)->shouldReturn(false);
    }

    function it_returns_true_if_order_refunded_total_is_equal_to_order_total(
        OrderInterface $order,
        OrderRefundedTotalProviderInterface $orderRefundedTotalProvider,
    ): void {
        $order->getTotal()->willReturn(1000);

        $orderRefundedTotalProvider->__invoke($order)->willReturn(1000);

        $this->isOrderFullyRefunded($order)->shouldReturn(true);
    }
}

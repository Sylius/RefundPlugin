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

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundSpec extends ObjectBehavior
{
    function let(
        OrderInterface $order,
        OrderItemUnitInterface $orderItemUnit,
    ): void {
        $this->beConstructedWith($order, 1000, $orderItemUnit, RefundType::orderItemUnit());
    }

    function it_implements_refund_interface(): void
    {
        $this->shouldImplement(RefundInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_order(OrderInterface $order): void
    {
        $order->getNumber()->willReturn('000555');

        $this->getOrder()->shouldReturn($order);
        $this->getOrderNumber()->shouldReturn('000555');
    }

    function it_has_amount(): void
    {
        $this->getAmount()->shouldReturn(1000);
    }

    function it_has_order_item_unit(OrderItemUnitInterface $orderItemUnit): void
    {
        $this->getOrderItemUnit()->shouldReturn($orderItemUnit);
    }

    function it_has_type(): void
    {
        $this->getType()->shouldBeLike(RefundType::orderItemUnit());
    }
}

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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentSpec extends ObjectBehavior
{
    function let(OrderInterface $order, PaymentMethodInterface $paymentMethod): void
    {
        $this->beConstructedWith($order, 100, 'USD', RefundPaymentInterface::STATE_NEW, $paymentMethod);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RefundPayment::class);
    }

    function it_implements_refund_payment_interface(): void
    {
        $this->shouldImplement(RefundPaymentInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_an_order(OrderInterface $order): void
    {
        $order->getNumber()->willReturn('000002');

        $this->getOrder()->shouldReturn($order);
        $this->getOrderNumber()->shouldReturn('000002');
    }

    function it_has_amount(): void
    {
        $this->getAmount()->shouldReturn(100);
    }

    function it_has_currency_code(): void
    {
        $this->getCurrencyCode()->shouldReturn('USD');
    }

    function it_has_state(): void
    {
        $this->getState()->shouldReturn(RefundPaymentInterface::STATE_NEW);
    }

    function it_has_payment_method(): void
    {
        $this->getPaymentMethod()->shouldBeAnInstanceOf(PaymentMethodInterface::class);
    }
}

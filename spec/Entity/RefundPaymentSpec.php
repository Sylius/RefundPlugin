<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentSpec extends ObjectBehavior
{
    function let(PaymentMethodInterface $paymentMethod): void
    {
        $this->beConstructedWith('000002', 100, 'USD', RefundPaymentInterface::STATE_NEW, $paymentMethod);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RefundPayment::class);
    }

    function it_implements_refund_payment_interface(): void
    {
        $this->shouldImplement(RefundPaymentInterface::class);
    }

    function it_has_order_number(): void
    {
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

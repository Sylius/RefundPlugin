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

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Exception\CompletedPaymentNotFound;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;

final class DefaultRelatedPaymentIdProviderSpec extends ObjectBehavior
{
    function it_implements_related_payment_id_provider_interface(): void
    {
        $this->shouldImplement(RelatedPaymentIdProviderInterface::class);
    }

    function it_provides_id_of_last_completed_payment_from_refund_payment_order(
        RefundPaymentInterface $refundPayment,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $refundPayment->getOrder()->willReturn($order);

        $order->getLastPayment(PaymentInterface::STATE_COMPLETED)->willReturn($payment);

        $payment->getId()->willReturn(4);

        $this->getForRefundPayment($refundPayment)->shouldReturn(4);
    }

    function it_throws_exception_if_order_has_no_completed_payments(
        RefundPaymentInterface $refundPayment,
        OrderInterface $order,
    ): void {
        $refundPayment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('000666');

        $order->getLastPayment(PaymentInterface::STATE_COMPLETED)->willReturn(null);

        $this
            ->shouldThrow(CompletedPaymentNotFound::withNumber('000666'))
            ->during('getForRefundPayment', [$refundPayment])
        ;
    }
}

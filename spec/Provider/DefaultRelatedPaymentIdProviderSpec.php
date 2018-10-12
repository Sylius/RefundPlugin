<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Exception\CompletedPaymentNotFound;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;

final class DefaultRelatedPaymentIdProviderSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_implements_related_payment_id_provider_interface(): void
    {
        $this->shouldImplement(RelatedPaymentIdProviderInterface::class);
    }

    function it_provides_id_of_last_completed_payment_from_refund_payment_order(
        OrderRepositoryInterface $orderRepository,
        RefundPaymentInterface $refundPayment,
        OrderInterface $order,
        PaymentInterface $payment
    ): void {
        $refundPayment->getOrderNumber()->willReturn('000333');

        $orderRepository->findOneByNumber('000333')->willReturn($order);

        $order->getLastPayment(PaymentInterface::STATE_COMPLETED)->willReturn($payment);

        $payment->getId()->willReturn(4);

        $this->getForRefundPayment($refundPayment)->shouldReturn(4);
    }

    function it_throws_exception_if_there_is_no_order_with_given_number(
        OrderRepositoryInterface $orderRepository,
        RefundPaymentInterface $refundPayment
    ): void {
        $refundPayment->getOrderNumber()->willReturn('000666');
        $orderRepository->findOneByNumber('000666')->willReturn(null);

        $this
            ->shouldThrow(OrderNotFound::withNumber('000666'))
            ->during('getForRefundPayment', [$refundPayment])
        ;
    }

    function it_throws_exception_if_order_has_no_completed_payments(
        OrderRepositoryInterface $orderRepository,
        RefundPaymentInterface $refundPayment,
        OrderInterface $order
    ): void {
        $refundPayment->getOrderNumber()->willReturn('000666');
        $orderRepository->findOneByNumber('000666')->willReturn($order);

        $order->getLastPayment(PaymentInterface::STATE_COMPLETED)->willReturn(null);

        $this
            ->shouldThrow(CompletedPaymentNotFound::withNumber('000666'))
            ->during('getForRefundPayment', [$refundPayment])
        ;
    }
}

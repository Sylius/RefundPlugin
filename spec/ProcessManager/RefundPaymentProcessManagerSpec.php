<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\ProcessManager;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;

final class RefundPaymentProcessManagerSpec extends ObjectBehavior
{
    function let(
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        EntityManagerInterface $entityManager,
        EventBus $eventBus
    ): void {
        $this->beConstructedWith(
            $orderFullyRefundedStateResolver,
            $refundPaymentFactory,
            $entityManager,
            $eventBus
        );
    }

    function it_reacts_on_units_refunded_event_and_creates_refund_payment(
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        EntityManagerInterface $entityManager,
        RefundPaymentInterface $refundPayment,
        EventBus $eventBus
    ): void {
        $refundPaymentFactory->createWithData(
            '000222',
            1000,
            'USD',
            RefundPaymentInterface::STATE_NEW,
            1
        )->willReturn($refundPayment);

        $entityManager->persist($refundPayment)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $orderFullyRefundedStateResolver->resolve('000222')->shouldBeCalled();

        $refundPayment->getId()->willReturn(10);
        $refundPayment->getOrderNumber()->willReturn('000222');
        $refundPayment->getAmount()->willReturn(1000);

        $eventBus->dispatch(Argument::that(function (RefundPaymentGenerated $event): bool {
            return
                $event->id() === 10 &&
                $event->orderNumber() === '000222' &&
                $event->amount() === 1000 &&
                $event->currencyCode() === 'USD' &&
                $event->paymentMethodId() === 1
            ;
        }))->shouldBeCalled();

        $this(new UnitsRefunded('000222', [new OrderItemUnitRefund(1, 500), new OrderItemUnitRefund(2, 500)], [1], 1, 1000, 'USD', 'Comment'));
    }
}

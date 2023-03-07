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

namespace spec\Sylius\RefundPlugin\ProcessManager;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundPaymentProcessManagerSpec extends ObjectBehavior
{
    function let(
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RelatedPaymentIdProviderInterface $relatedPaymentIdProvider,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
    ): void {
        $this->beConstructedWith(
            $orderFullyRefundedStateResolver,
            $relatedPaymentIdProvider,
            $refundPaymentFactory,
            $orderRepository,
            $paymentMethodRepository,
            $entityManager,
            $eventBus,
        );
    }

    function it_implements_units_refunded_process_step_interface(): void
    {
        $this->shouldImplement(UnitsRefundedProcessStepInterface::class);
    }

    function it_reacts_on_units_refunded_event_and_creates_refund_payment(
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RelatedPaymentIdProviderInterface $relatedPaymentIdProvider,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        RefundPaymentInterface $refundPayment,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethod,
        UnitRefundInterface $firstUnitRefund,
        UnitRefundInterface $secondUnitRefund,
    ): void {
        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $paymentMethodRepository->find(1)->willReturn($paymentMethod);

        $refundPaymentFactory
            ->createWithData($order, 1000, 'USD', RefundPaymentInterface::STATE_NEW, $paymentMethod)
            ->willReturn($refundPayment)
        ;

        $entityManager->persist($refundPayment)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $orderFullyRefundedStateResolver->resolve('000222')->shouldBeCalled();

        $refundPayment->getId()->willReturn(10);
        $refundPayment->getOrder()->willReturn($order);
        $refundPayment->getAmount()->willReturn(1000);

        $relatedPaymentIdProvider->getForRefundPayment($refundPayment)->willReturn(3);

        $event = new RefundPaymentGenerated(10, '000222', 1000, 'USD', 1, 3);
        $eventBus->dispatch($event)->willReturn(new Envelope($event))->shouldBeCalled();

        $this->next(new UnitsRefunded(
            '000222',
            [$firstUnitRefund->getWrappedObject(), $secondUnitRefund->getWrappedObject()],
            1,
            1000,
            'USD',
            'Comment',
        ));
    }
}

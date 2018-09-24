<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class UnitsRefundedEventListenerSpec extends ObjectBehavior
{
    function let(
        Session $session,
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        EntityManagerInterface $entityManager
    ): void {
        $this->beConstructedWith(
            $session,
            $orderFullyRefundedStateResolver,
            $refundPaymentFactory,
            $entityManager
        );
    }

    function it_listens_to_units_refunded_event_and_add_success_flash_after_it_occurs(
        Session $session,
        FlashBagInterface $flashBag,
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        EntityManagerInterface $entityManager,
        RefundPaymentInterface $refundPayment
    ): void {
        $refundPaymentFactory->createWithData(
            '000222',
            1000,
            'USD',
            RefundPaymentInterface::STATE_NEW, 1
        )->willReturn($refundPayment);

        $entityManager->persist($refundPayment)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $orderFullyRefundedStateResolver->resolve('000222')->shouldBeCalled();

        $session->getFlashBag()->willReturn($flashBag);

        $flashBag->add('success', 'sylius_refund.units_successfully_refunded')->shouldBeCalled();

        $this(new UnitsRefunded('000222', [new OrderItemUnitRefund(1, 500), new OrderItemUnitRefund(2, 500)], [1], 1, 1000, 'USD', 'Comment'));
    }
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
use Sylius\RefundPlugin\Generator\NumberGenerator;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class UnitsRefundedEventListener
{
    /** @var Session */
    private $session;

    /** @var OrderFullyRefundedStateResolverInterface */
    private $orderFullyRefundedStateResolver;

    /** @var NumberGenerator */
    private $numberGenerator;

    /** @var RefundPaymentFactoryInterface */
    private $refundPaymentFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        Session $session,
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        NumberGenerator $numberGenerator,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->session = $session;
        $this->orderFullyRefundedStateResolver = $orderFullyRefundedStateResolver;
        $this->numberGenerator = $numberGenerator;
        $this->refundPaymentFactory = $refundPaymentFactory;
        $this->entityManager = $entityManager;
    }

    public function __invoke(UnitsRefunded $event): void
    {
        $refundPayment = $this->refundPaymentFactory->createWithData(
            $this->numberGenerator->generate(),
            $event->orderNumber(),
            $event->amount(),
            $event->currencyCode(),
            RefundPaymentInterface::STATE_NEW,
            $event->paymentMethodId()
        );

        $this->entityManager->persist($refundPayment);
        $this->entityManager->flush();

        $this->orderFullyRefundedStateResolver->resolve($event->orderNumber());
        $this->session->getFlashBag()->add('success', 'sylius_refund.units_successfully_refunded');
    }
}

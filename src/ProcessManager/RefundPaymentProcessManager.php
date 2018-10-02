<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ProcessManager;

use Doctrine\ORM\EntityManagerInterface;
use Prooph\ServiceBus\EventBus;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;

final class RefundPaymentProcessManager
{
    /** @var OrderFullyRefundedStateResolverInterface */
    private $orderFullyRefundedStateResolver;

    /** @var RefundPaymentFactoryInterface */
    private $refundPaymentFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        EntityManagerInterface $entityManager,
        EventBus $eventBus
    ) {
        $this->orderFullyRefundedStateResolver = $orderFullyRefundedStateResolver;
        $this->refundPaymentFactory = $refundPaymentFactory;
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
    }

    public function __invoke(UnitsRefunded $event): void
    {
        $refundPayment = $this->refundPaymentFactory->createWithData(
            $event->orderNumber(),
            $event->amount(),
            $event->currencyCode(),
            RefundPaymentInterface::STATE_NEW,
            $event->paymentMethodId()
        );

        $this->entityManager->persist($refundPayment);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new RefundPaymentGenerated(
            $refundPayment->getId(),
            $event->orderNumber(),
            $event->amount(),
            $event->currencyCode(),
            $event->paymentMethodId(),
            1
        ));

        $this->orderFullyRefundedStateResolver->resolve($event->orderNumber());
    }
}

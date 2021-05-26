<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ProcessManager;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundPaymentProcessManager implements UnitsRefundedProcessStepInterface
{
    /** @var OrderFullyRefundedStateResolverInterface */
    private $orderFullyRefundedStateResolver;

    /** @var RelatedPaymentIdProviderInterface */
    private $relatedPaymentIdProvider;

    /** @var RefundPaymentFactoryInterface */
    private $refundPaymentFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MessageBusInterface */
    private $eventBus;

    public function __construct(
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RelatedPaymentIdProviderInterface $relatedPaymentIdProvider,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus
    ) {
        $this->orderFullyRefundedStateResolver = $orderFullyRefundedStateResolver;
        $this->relatedPaymentIdProvider = $relatedPaymentIdProvider;
        $this->refundPaymentFactory = $refundPaymentFactory;
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
    }

    public function next(UnitsRefunded $unitsRefunded): void
    {
        $refundPayment = $this->refundPaymentFactory->createWithData(
            $unitsRefunded->orderNumber(),
            $unitsRefunded->amount(),
            $unitsRefunded->currencyCode(),
            RefundPaymentInterface::STATE_NEW,
            $unitsRefunded->paymentMethodId()
        );

        $this->entityManager->persist($refundPayment);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new RefundPaymentGenerated(
            $refundPayment->getId(),
            $unitsRefunded->orderNumber(),
            $unitsRefunded->amount(),
            $unitsRefunded->currencyCode(),
            $unitsRefunded->paymentMethodId(),
            $this->relatedPaymentIdProvider->getForRefundPayment($refundPayment)
        ));

        $this->orderFullyRefundedStateResolver->resolve($unitsRefunded->orderNumber());
    }
}

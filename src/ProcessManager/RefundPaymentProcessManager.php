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

namespace Sylius\RefundPlugin\ProcessManager;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class RefundPaymentProcessManager implements UnitsRefundedProcessStepInterface
{
    /** @var OrderFullyRefundedStateResolverInterface */
    private $orderFullyRefundedStateResolver;

    /** @var RelatedPaymentIdProviderInterface */
    private $relatedPaymentIdProvider;

    /** @var RefundPaymentFactoryInterface */
    private $refundPaymentFactory;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MessageBusInterface */
    private $eventBus;

    public function __construct(
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RelatedPaymentIdProviderInterface $relatedPaymentIdProvider,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        OrderRepositoryInterface $orderRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus
    ) {
        $this->orderFullyRefundedStateResolver = $orderFullyRefundedStateResolver;
        $this->relatedPaymentIdProvider = $relatedPaymentIdProvider;
        $this->refundPaymentFactory = $refundPaymentFactory;
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
    }

    public function next(UnitsRefunded $unitsRefunded): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($unitsRefunded->orderNumber());
        Assert::notNull($order);

        $refundPayment = $this->refundPaymentFactory->createWithData(
            $order,
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

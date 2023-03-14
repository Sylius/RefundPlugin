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
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
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
    private OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver;

    private RelatedPaymentIdProviderInterface $relatedPaymentIdProvider;

    private RefundPaymentFactoryInterface $refundPaymentFactory;

    private OrderRepositoryInterface $orderRepository;

    private PaymentMethodRepositoryInterface $paymentMethodRepository;

    private EntityManagerInterface $entityManager;

    private MessageBusInterface $eventBus;

    public function __construct(
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
        RelatedPaymentIdProviderInterface $relatedPaymentIdProvider,
        RefundPaymentFactoryInterface $refundPaymentFactory,
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
    ) {
        $this->orderFullyRefundedStateResolver = $orderFullyRefundedStateResolver;
        $this->relatedPaymentIdProvider = $relatedPaymentIdProvider;
        $this->refundPaymentFactory = $refundPaymentFactory;
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
    }

    public function next(UnitsRefunded $unitsRefunded): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($unitsRefunded->orderNumber());
        Assert::notNull($order);

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->find($unitsRefunded->paymentMethodId());
        Assert::notNull($paymentMethod);

        $refundPayment = $this->refundPaymentFactory->createWithData(
            $order,
            $unitsRefunded->amount(),
            $unitsRefunded->currencyCode(),
            RefundPaymentInterface::STATE_NEW,
            $paymentMethod,
        );

        $this->entityManager->persist($refundPayment);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new RefundPaymentGenerated(
            $refundPayment->getId(),
            $unitsRefunded->orderNumber(),
            $unitsRefunded->amount(),
            $unitsRefunded->currencyCode(),
            $unitsRefunded->paymentMethodId(),
            $this->relatedPaymentIdProvider->getForRefundPayment($refundPayment),
        ));

        $this->orderFullyRefundedStateResolver->resolve($unitsRefunded->orderNumber());
    }
}

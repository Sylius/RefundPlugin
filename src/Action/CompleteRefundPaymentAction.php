<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplierInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

final class CompleteRefundPaymentAction
{
    /** @var Session */
    private $session;

    /** @var ObjectRepository */
    private $refundPaymentRepository;

    /** @var RefundPaymentCompletedStateApplierInterface */
    private $refundPaymentCompletedStateApplier;

    /** @var RouterInterface */
    private $router;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var RelatedPaymentIdProviderInterface */
    private $relatedPaymentIdProvider;

    public function __construct(
        Session $session,
        ObjectRepository $refundPaymentInterface,
        OrderRepositoryInterface $orderRepository,
        RefundPaymentCompletedStateApplierInterface $refundPaymentCompletedStateApplier,
        RouterInterface $router,
        MessageBusInterface $messageBus,
        RelatedPaymentIdProviderInterface $relatedPaymentIdProvider
    ) {
        $this->session = $session;
        $this->refundPaymentRepository = $refundPaymentInterface;
        $this->refundPaymentCompletedStateApplier = $refundPaymentCompletedStateApplier;
        $this->router = $router;
        $this->orderRepository = $orderRepository;
        $this->messageBus = $messageBus;
        $this->relatedPaymentIdProvider = $relatedPaymentIdProvider;
    }

    public function __invoke(Request $request, string $orderNumber, string $id): Response
    {
        /** @var RefundPaymentInterface $refundPayment */
        $refundPayment = $this->refundPaymentRepository->find($id);

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        try {
            $this->messageBus->dispatch(new RefundPaymentGenerated(
                $refundPayment->getId(),
                $refundPayment->getOrderNumber(),
                $refundPayment->getAmount(),
                $refundPayment->getCurrencyCode(),
                $refundPayment->getPaymentMethod()->getId(),
                $this->relatedPaymentIdProvider->getForRefundPayment($refundPayment)
            ));
            $this->refundPaymentCompletedStateApplier->apply($refundPayment);
            $this->session->getFlashBag()->add('success', 'sylius_refund.refund_payment_completed');
        } catch (\Throwable $throwable) {
            $this->session->getFlashBag()->add('error', $throwable->getMessage());
        }

        return new RedirectResponse($this->router->generate(
            'sylius_admin_order_show',
            ['id' => $order->getId()]));
    }
}

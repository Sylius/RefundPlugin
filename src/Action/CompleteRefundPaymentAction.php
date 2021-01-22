<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action;

use Doctrine\Persistence\ObjectRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplierInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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

    public function __construct(
        Session $session,
        ObjectRepository $refundPaymentInterface,
        OrderRepositoryInterface $orderRepository,
        RefundPaymentCompletedStateApplierInterface $refundPaymentCompletedStateApplier,
        RouterInterface $router
    ) {
        $this->session = $session;
        $this->refundPaymentRepository = $refundPaymentInterface;
        $this->refundPaymentCompletedStateApplier = $refundPaymentCompletedStateApplier;
        $this->router = $router;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(Request $request, string $orderNumber, string $id): Response
    {
        /** @var RefundPaymentInterface $refundPayment */
        $refundPayment = $this->refundPaymentRepository->find($id);

        $this->refundPaymentCompletedStateApplier->apply($refundPayment);

        $this->session->getFlashBag()->add('success', 'sylius_refund.refund_payment_completed');

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        return new RedirectResponse($this->router->generate(
            'sylius_admin_order_show',
            ['id' => $order->getId()]));
    }
}

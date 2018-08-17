<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplierInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

final class CompleteRefundPaymentAction
{
    /** @var Session */
    private $session;

    /** @var ObjectRepository */
    private $refundPaymentRepository;

    /** @var RefundPaymentCompletedStateApplierInterface */
    private $refundPaymentCompletedStateApplier;

    public function __construct(
        Session $session,
        ObjectRepository $refundPaymentInterface,
        RefundPaymentCompletedStateApplierInterface $refundPaymentCompletedStateApplier
    ) {
        $this->session = $session;
        $this->refundPaymentRepository = $refundPaymentInterface;
        $this->refundPaymentCompletedStateApplier = $refundPaymentCompletedStateApplier;
    }

    public function __invoke(Request $request, string $id): Response
    {
        /** @var RefundPaymentInterface $refundPayment */
        $refundPayment = $this->refundPaymentRepository->find($id);

        $this->refundPaymentCompletedStateApplier->apply($refundPayment);

        $this->session->getFlashBag()->add('sucess', 'sylius_refund.refund_payment_completed');

        return new Response('', Response::HTTP_OK);
    }
}

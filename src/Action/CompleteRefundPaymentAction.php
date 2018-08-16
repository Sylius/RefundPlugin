<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CompleteRefundPaymentAction
{
    public function __invoke(Request $request, string $orderNumber, string $refundPaymentId): Response
    {
        // TODO: Implement __invoke() method.
    }
}

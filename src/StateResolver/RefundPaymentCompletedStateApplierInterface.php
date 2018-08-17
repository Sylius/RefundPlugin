<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RefundPaymentCompletedStateApplierInterface
{
    public function apply(RefundPaymentInterface $refundPayment): void;
}

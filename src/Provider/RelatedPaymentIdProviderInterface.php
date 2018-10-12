<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RelatedPaymentIdProviderInterface
{
    public function getForRefundPayment(RefundPaymentInterface $refundPayment): int;
}

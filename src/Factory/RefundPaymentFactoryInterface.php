<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RefundPaymentFactoryInterface
{
    public function createWithData(
        string $number,
        int $amount,
        string $currencyCode,
        string $state,
        int $paymentMethodId
    ): RefundPaymentInterface;
}

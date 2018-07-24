<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

interface OrderFullyRefundedStateResolverInterface
{
    public function resolve(string $orderNumber): void;
}

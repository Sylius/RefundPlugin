<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

interface OrderPartiallyRefundedStateResolverInterface
{
    public function resolve(string $orderNumber): void;
}

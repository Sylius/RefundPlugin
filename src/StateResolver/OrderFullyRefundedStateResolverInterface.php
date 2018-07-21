<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderFullyRefundedStateResolverInterface
{
    public function resolve(OrderInterface $order): void;
}

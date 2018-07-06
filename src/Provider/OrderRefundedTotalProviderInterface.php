<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

interface OrderRefundedTotalProviderInterface
{
    public function __invoke(string $orderNumber): int;
}

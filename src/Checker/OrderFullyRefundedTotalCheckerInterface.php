<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderFullyRefundedTotalCheckerInterface
{
    public function isOrderFullyRefunded(OrderInterface $order): bool;
}

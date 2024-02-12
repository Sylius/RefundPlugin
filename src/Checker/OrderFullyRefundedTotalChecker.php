<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderFullyRefundedTotalChecker implements OrderFullyRefundedTotalCheckerInterface
{
    private OrderRefundedTotalProviderInterface $orderRefundedTotalProvider;

    public function __construct(OrderRefundedTotalProviderInterface $orderRefundedTotalProvider)
    {
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
    }

    public function isOrderFullyRefunded(OrderInterface $order): bool
    {
        return $order->getTotal() === $this->orderRefundedTotalProvider->__invoke($order);
    }
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;
use Webmozart\Assert\Assert;

final class OrderFullyRefundedTotalChecker implements OrderFullyRefundedTotalCheckerInterface
{
    /** @var OrderRefundedTotalProviderInterface */
    private $orderRefundedTotalProvider;

    public function __construct(OrderRefundedTotalProviderInterface $orderRefundedTotalProvider)
    {
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
    }

    public function isOrderFullyRefunded(OrderInterface $order): bool
    {
        /** @var string|null $orderNumber */
        $orderNumber = $order->getNumber();
        Assert::notNull($orderNumber);

        return $order->getTotal() === $this->orderRefundedTotalProvider->__invoke($orderNumber);
    }
}

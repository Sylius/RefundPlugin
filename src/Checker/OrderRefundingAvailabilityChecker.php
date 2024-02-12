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
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class OrderRefundingAvailabilityChecker implements OrderRefundingAvailabilityCheckerInterface
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(string $orderNumber): bool
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        return
            in_array($order->getPaymentState(), [
                OrderPaymentStates::STATE_PAID,
                OrderPaymentStates::STATE_PARTIALLY_REFUNDED,
            ], true) &&
            $order->getTotal() !== 0
        ;
    }
}

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

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;

final class OrderRefundedTotalProvider implements OrderRefundedTotalProviderInterface
{
    private RepositoryInterface $refundRepository;

    public function __construct(RepositoryInterface $refundRepository)
    {
        $this->refundRepository = $refundRepository;
    }

    public function __invoke(OrderInterface $order): int
    {
        $refunds = $this->refundRepository->findBy(['order' => $order]);

        $orderRefundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            $orderRefundedTotal += $refund->getAmount();
        }

        return $orderRefundedTotal;
    }
}

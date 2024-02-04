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

namespace Sylius\RefundPlugin\Doctrine\ORM;

use Sylius\Component\Core\Repository\OrderItemUnitRepositoryInterface;

final class CountOrderItemUnitRefundsBelongingToOrderQuery implements CountRefundsBelongingToOrderQueryInterface
{
    public function __construct(private OrderItemUnitRepositoryInterface $orderItemUnitRepository)
    {
    }

    public function count(array $unitRefundIds, string $orderNumber): int
    {
        return (int) $this->orderItemUnitRepository->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->innerJoin('o.orderItem', 'orderItem')
            ->innerJoin('orderItem.order', 'ord')
            ->andWhere('o.id IN (:unitRefundIds)')
            ->andWhere('ord.number = :orderNumber')
            ->setParameter('unitRefundIds', $unitRefundIds)
            ->setParameter('orderNumber', $orderNumber)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

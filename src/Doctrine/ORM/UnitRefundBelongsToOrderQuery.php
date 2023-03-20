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

namespace Sylius\RefundPlugin\Doctrine\ORM;

use Sylius\Component\Core\Repository\OrderItemUnitRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class UnitRefundBelongsToOrderQuery implements UnitRefundBelongsToOrderQueryInterface
{
    public function __construct(
        private OrderItemUnitRepositoryInterface $orderItemUnitRepository,
        private RepositoryInterface $adjustmentRepository,
    ) {
    }

    public function orderItemUnitRefundsBelongToOrder(array $unitRefundIds, string $orderNumber): bool
    {
        $unitsBelongingToOrderCount = (int) $this->orderItemUnitRepository->createQueryBuilder('o')
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

        return count($unitRefundIds) === $unitsBelongingToOrderCount;
    }

    public function shipmentRefundsBelongToOrder(array $unitRefundIds, string $orderNumber): bool
    {
        $unitsBelongingToOrderCount = (int) $this->adjustmentRepository->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->innerJoin('o.order', 'ord')
            ->andWhere('o.id IN (:unitRefundIds)')
            ->andWhere('ord.number = :orderNumber')
            ->setParameter('unitRefundIds', $unitRefundIds)
            ->setParameter('orderNumber', $orderNumber)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return count($unitRefundIds) === $unitsBelongingToOrderCount;
    }
}

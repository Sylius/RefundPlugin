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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\RefundPlugin\Repository\RefundPaymentRepositoryInterface;

class RefundPaymentRepository extends EntityRepository implements RefundPaymentRepositoryInterface
{
    public function findByOrderNumber(string $orderNumber): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.order', 'ord')
            ->andWhere('ord.number = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->getQuery()
            ->getResult()
        ;
    }
}

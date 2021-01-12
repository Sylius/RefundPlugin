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
use Sylius\RefundPlugin\Repository\CreditMemoRepositoryInterface;

class CreditMemoRepository extends EntityRepository implements CreditMemoRepositoryInterface
{
    public function findByOrderId(string $orderId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.order = :orderId')
            ->addOrderBy('o.issuedAt', 'ASC')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getResult()
        ;
    }
}

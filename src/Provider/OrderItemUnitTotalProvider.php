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

use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;
use Webmozart\Assert\Assert;

final class OrderItemUnitTotalProvider implements RefundUnitTotalProviderInterface
{
    public function __construct(
        private RepositoryInterface $orderItemUnitRepository,
    ) {
    }

    public function getRefundUnitTotal(int $id): int
    {
        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $this->orderItemUnitRepository->find($id);
        Assert::notNull($orderItemUnit);

        return $orderItemUnit->getTotal();
    }

    public static function refundType(): string
    {
        return RefundTypeInterface::ORDER_ITEM_UNIT;
    }
}

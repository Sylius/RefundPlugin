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

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\Refund;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;

final class RefundFactory implements RefundFactoryInterface
{
    public function createNew(): RefundInterface
    {
        throw new \InvalidArgumentException('This object is not default constructable.');
    }

    public function createWithData(
        OrderInterface $order,
        int $unitId,
        int $amount,
        RefundTypeInterface $type
    ): RefundInterface {
        return new Refund($order, $amount, $unitId, $type);
    }
}

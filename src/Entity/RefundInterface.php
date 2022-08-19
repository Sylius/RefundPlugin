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

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;

interface RefundInterface extends ResourceInterface
{
    public function getOrder(): OrderInterface;

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use RefundInterface::getOrder() instead */
    public function getOrderNumber(): string;

    public function getAmount(): int;

    public function getOrderItemUnit(): OrderItemUnitInterface;

    public function getType(): RefundTypeInterface;
}

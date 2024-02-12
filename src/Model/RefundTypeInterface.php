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

namespace Sylius\RefundPlugin\Model;

interface RefundTypeInterface
{
    public const ORDER_ITEM_UNIT = 'order_item_unit';

    public const SHIPMENT = 'shipment';

    public static function orderItemUnit(): self;

    public static function shipment(): self;

    /** @psalm-return mixed */
    public function getValue();

    /** @psalm-param mixed $variable */
    public function equals($variable = null): bool;
}

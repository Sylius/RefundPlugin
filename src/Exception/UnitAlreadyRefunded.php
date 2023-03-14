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

namespace Sylius\RefundPlugin\Exception;

final class UnitAlreadyRefunded extends \InvalidArgumentException
{
    public static function withIdAndOrderNumber(int $unitId, string $orderNumber): self
    {
        return new self(sprintf(
            'Unit with id "%d" from order with number "%s" has already been refunded',
            $unitId,
            $orderNumber,
        ));
    }
}

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

namespace Sylius\RefundPlugin\Exception;

final class RefundTypeNotResolved extends \InvalidArgumentException
{
    public static function withType(string $type): self
    {
        return new self(sprintf('Refund type "%d" could not be resolved', $type));
    }
}

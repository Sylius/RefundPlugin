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

final class CreditMemoNotFound extends \InvalidArgumentException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Credit memo with id "%s" has not been found', $id));
    }

    public static function withNumber(string $number): self
    {
        return new self(sprintf('Credit memo with number "%s" has not been found', $number));
    }
}

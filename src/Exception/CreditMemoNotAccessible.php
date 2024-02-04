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

final class CreditMemoNotAccessible extends \InvalidArgumentException
{
    public static function withUserId(string $creditMemoId, int $userId): self
    {
        return new self(sprintf('Credit memo with id "%s" is not accessible for user with id %s', $creditMemoId, $userId));
    }
}

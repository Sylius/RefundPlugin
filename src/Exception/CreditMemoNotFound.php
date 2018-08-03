<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class CreditMemoNotFound extends \InvalidArgumentException
{
    public static function withId(int $id): self
    {
        return new self(sprintf('Credit memo with id "%d" has not been found', $id));
    }
}

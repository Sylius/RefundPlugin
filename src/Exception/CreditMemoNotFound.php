<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class CreditMemoNotFound extends \InvalidArgumentException
{
    public static function withId(int $id): self
    {
        return new self(sprintf('Credit memo with id "%d" has not been found', $id));
    }

    public static function withNumber(string $number): self
    {
        return new self(sprintf('Credit memo with number "%s" has not been found', $number));
    }
}

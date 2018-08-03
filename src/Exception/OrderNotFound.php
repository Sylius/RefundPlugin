<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class OrderNotFound extends \InvalidArgumentException
{
    public static function withNumber(string $orderNumber): self
    {
        return new self(sprintf('Order with number "%s" has not been found', $orderNumber));
    }
}

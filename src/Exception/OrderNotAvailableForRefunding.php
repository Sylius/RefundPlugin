<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class OrderNotAvailableForRefunding extends \InvalidArgumentException
{
    public static function withOrderNumber(string $orderNumber): self
    {
        return new self(sprintf('Order with number "%s" has not been paid yet', $orderNumber));
    }
}

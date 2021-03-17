<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class UnitAlreadyRefunded extends \InvalidArgumentException
{
    public static function withIdAndOrderNumber(int $unitId, string $orderNumber): self
    {
        return new self(sprintf(
            'Unit with id "%d" from order with number "%s" has already been refunded', $unitId, $orderNumber
        ));
    }
}

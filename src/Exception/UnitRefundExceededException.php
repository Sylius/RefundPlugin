<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class UnitRefundExceededException extends \InvalidArgumentException
{
    public static function withIdAndOrderNumber(int $unitId, string $orderNumber): self
    {
        return new self(sprintf(
            'Unit with id "%d" from order with number "%s" cannot be refunded with such an amount', $unitId, $orderNumber
        ));
    }
}

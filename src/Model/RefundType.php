<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

use Sylius\RefundPlugin\Exception\RefundTypeNotResolved;

final class RefundType
{
    public const ORDER_UNIT = 'order_unit';
    public const SHIPMENT = 'shipment';

    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::ORDER_UNIT, self::SHIPMENT])) {
            throw RefundTypeNotResolved::withType($value);
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function orderUnit(): self
    {
        return new self(self::ORDER_UNIT);
    }

    public static function shipment(): self
    {
        return new self(self::SHIPMENT);
    }
}

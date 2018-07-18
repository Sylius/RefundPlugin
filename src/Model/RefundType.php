<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class RefundType
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function orderUnit(): self
    {
        return new self('order_unit');
    }

    public static function shipment(): self
    {
        return new self('shipment');
    }
}

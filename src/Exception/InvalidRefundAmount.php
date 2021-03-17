<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class InvalidRefundAmount extends \InvalidArgumentException
{
    public static function withValidationConstraint(string $validationConstraint): self
    {
        return new self($validationConstraint);
    }
}

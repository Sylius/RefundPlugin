<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class RefundTypeNotResolved extends \InvalidArgumentException
{
    public static function withType(string $type): self
    {
        return new self(sprintf('Refund type "%d" could not be resolved', $type));
    }
}

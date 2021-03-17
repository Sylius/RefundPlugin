<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class MoreThanOneTaxAdjustment extends \InvalidArgumentException
{
    public static function occur(): self
    {
        return new self('Each adjustable entity must not have more than 1 tax adjustment');
    }
}

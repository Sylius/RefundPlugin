<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class LineItemsCannotBeMerged extends \InvalidArgumentException
{
    public static function occur(): self
    {
        return new self('Line items cannot be merged');
    }
}

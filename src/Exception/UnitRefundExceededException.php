<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class UnitRefundExceededException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('You cannot refund more money than the refunded unit total');
    }
}

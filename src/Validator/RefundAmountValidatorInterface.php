<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Model\RefundType;

interface RefundAmountValidatorInterface
{
    public function validateUnits(array $refunds, RefundType $refundType): void;
}

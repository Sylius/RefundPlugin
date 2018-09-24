<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Command\RefundUnits;

interface RefundUnitsCommandValidatorInterface
{
    public function validate(RefundUnits $command): void;
}

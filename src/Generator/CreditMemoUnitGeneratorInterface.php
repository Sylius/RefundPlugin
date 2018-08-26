<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;

interface CreditMemoUnitGeneratorInterface
{
    public function generate(int $unitId, int $amount = null): CreditMemoUnitInterface;
}

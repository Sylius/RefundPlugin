<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

interface RefundedUnitTotalProviderInterface
{
    public function getTotalOfUnitWithId(int $unitId): int;
}

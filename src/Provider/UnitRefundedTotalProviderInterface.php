<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\RefundPlugin\Model\RefundType;

interface UnitRefundedTotalProviderInterface
{
    public function __invoke(int $unitId, RefundType $type): int;
}

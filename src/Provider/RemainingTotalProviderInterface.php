<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\RefundPlugin\Model\RefundType;

interface RemainingTotalProviderInterface
{
    public function getTotalLeftToRefund(int $id, RefundType $type): int;
}

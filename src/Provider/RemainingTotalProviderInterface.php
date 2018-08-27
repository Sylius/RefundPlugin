<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

interface RemainingTotalProviderInterface
{
    public function getTotalLeftToRefund(int $id): int;
}

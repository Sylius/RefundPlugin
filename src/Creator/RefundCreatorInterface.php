<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Sylius\RefundPlugin\Model\RefundType;

interface RefundCreatorInterface
{
    public function __invoke(string $orderNumber, int $unitId, int $amount, RefundType $refundType): void;
}

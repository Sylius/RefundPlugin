<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

interface RefundCreatorInterface
{
    public function __invoke(string $orderNumber, int $unitId, int $amount): void;
}

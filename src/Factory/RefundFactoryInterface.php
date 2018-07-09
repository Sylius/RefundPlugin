<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\RefundInterface;

interface RefundFactoryInterface
{
    public function createWithData(string $orderNumber, int $unitId, int $amount): RefundInterface;
}

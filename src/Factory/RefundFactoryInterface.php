<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;

interface RefundFactoryInterface extends FactoryInterface
{
    public function createWithData(string $orderNumber, int $unitId, int $amount, RefundType $type): RefundInterface;
}

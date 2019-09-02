<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\Refund;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundFactory implements RefundFactoryInterface
{
    public function createWithData(string $orderNumber, int $unitId, int $amount, RefundType $type): RefundInterface
    {
        return new Refund($orderNumber, $amount, $unitId, $type);
    }

    /**
     * @return object
     */
    public function createNew()
    {
        throw new \RuntimeException();
    }
}

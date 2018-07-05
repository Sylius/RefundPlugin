<?php

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\Refund;

final class RefundFactory implements RefundFactoryInterface
{
    public function createWithData(string $orderNumber, int $unitId, int $amount)
    {
        $refund = new Refund();
        $refund->setOrderNumber($orderNumber);
        $refund->setRefundedUnitId($unitId);
        $refund->setAmount($amount);

        return $refund;
    }
}

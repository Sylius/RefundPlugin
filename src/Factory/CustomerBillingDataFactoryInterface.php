<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;

interface CustomerBillingDataFactoryInterface
{
    public function createForOrder(OrderInterface $order): CustomerBillingDataInterface;
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemUnitInterface;

interface TaxRateProviderInterface
{
    public function provide(OrderItemUnitInterface $orderItemUnit): ?string;
}

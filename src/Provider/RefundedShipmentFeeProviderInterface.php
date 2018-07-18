<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

interface RefundedShipmentFeeProviderInterface
{
    public function getFeeOfShipment(int $adjustmentId): int;
}

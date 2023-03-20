<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

class ShipmentRefunded implements UnitRefundedInterface
{
    private string $orderNumber;

    private int $shipmentUnitId;

    private int $amount;

    public function __construct(string $orderNumber, int $shipmentUnitId, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->shipmentUnitId = $shipmentUnitId;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function shipmentUnitId(): int
    {
        return $this->shipmentUnitId;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}

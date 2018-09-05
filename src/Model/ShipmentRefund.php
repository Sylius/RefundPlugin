<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class ShipmentRefund
{
    /** @var int */
    private $shipmentId;

    /** @var int */
    private $total;

    public function __construct(int $shipmentId, int $total)
    {
        $this->shipmentId = $shipmentId;
        $this->total = $total;
    }

    public function shipmentId(): int
    {
        return $this->shipmentId;
    }

    public function total(): int
    {
        return $this->total;
    }
}

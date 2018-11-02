<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class GenerateCreditMemo
{
    /** @var string */
    private $orderNumber;

    /** @var int */
    private $total;

    /** @var array|OrderItemUnitRefund[] */
    private $units;

    /** @var array|ShipmentRefund[] */
    private $shipments;

    /** @var string */
    private $comment;

    public function __construct(string $orderNumber, int $total, array $units, array $shipments, string $comment)
    {
        $this->orderNumber = $orderNumber;
        $this->total = $total;
        $this->units = $units;
        $this->shipments = $shipments;
        $this->comment = $comment;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function total(): int
    {
        return $this->total;
    }

    /** @return array|OrderItemUnitRefund[] */
    public function units(): array
    {
        return $this->units;
    }

    /** @return array|ShipmentRefund[] */
    public function shipments(): array
    {
        return $this->shipments;
    }

    public function comment(): string
    {
        return $this->comment;
    }
}

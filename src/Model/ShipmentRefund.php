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

namespace Sylius\RefundPlugin\Model;

final class ShipmentRefund implements UnitRefundInterface
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

    public function id(): int
    {
        return $this->shipmentId;
    }

    public function total(): int
    {
        return $this->total;
    }
}

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

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;

/**
 * @internal
 *
 * This class is not covered by the backward compatibility promise and it will be removed after update Sylius to 1.9.
 * It is a duplication of a logic from Sylius to provide proper adjustments handling.
 */
class Adjustment extends BaseAdjustment implements AdjustmentInterface
{
    /** @var array */
    protected $details = [];

    /** @var ShipmentInterface|null */
    protected $shipment;

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
    }

    public function getShipment(): ?ShipmentInterface
    {
        return $this->shipment;
    }

    public function setShipment(?ShipmentInterface $shipment): void
    {
        if ($this->shipment === $shipment) {
            return;
        }

        if ($this->shipment !== null) {
            $this->shipment->removeAdjustment($this);
        }

        $this->shipment = $shipment;

        if ($shipment !== null) {
            $this->setAdjustable($this->shipment->getOrder());
        }
    }
}

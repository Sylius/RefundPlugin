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

use Sylius\Component\Core\Model\AdjustmentInterface as BaseAdjustmentInterface;

/**
 * @internal
 *
 * This class is not covered by the backward compatibility promise and it will be removed after update Sylius to 1.9.
 * It is a duplication of a logic from Sylius to provide proper adjustments handling.
 */
interface AdjustmentInterface extends BaseAdjustmentInterface
{
    public function getDetails(): array;

    public function setDetails(array $details): void;

    public function getShipment(): ?ShipmentInterface;

    public function setShipment(?ShipmentInterface $shipment): void;
}

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

use Sylius\Component\Core\Model\ShipmentInterface as BaseShipmentInterface;
use Sylius\Component\Order\Model\AdjustableInterface;

/**
 * @internal
 *
 * This class is not covered by the backward compatibility promise and it will be removed after update Sylius to 1.9.
 * It is a duplication of a logic from Sylius to provide proper adjustments handling.
 */
if (is_a(BaseShipmentInterface::class, AdjustableInterface::class, true)) {
    interface ShipmentInterface extends BaseShipmentInterface
    {
    }
} else {
    interface ShipmentInterface extends BaseShipmentInterface, AdjustableInterface
    {
    }
}

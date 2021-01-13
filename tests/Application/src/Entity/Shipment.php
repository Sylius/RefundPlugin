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

namespace Tests\Sylius\RefundPlugin\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\Shipment as BaseShipment;
use Sylius\RefundPlugin\Entity\ShipmentInterface;
use Sylius\RefundPlugin\Entity\ShipmentTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_shipment")
 */
class Shipment extends BaseShipment implements ShipmentInterface
{
    use ShipmentTrait;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, BaseAdjustmentInterface> $this->adjustments */
        $this->adjustments = new ArrayCollection();
    }
}

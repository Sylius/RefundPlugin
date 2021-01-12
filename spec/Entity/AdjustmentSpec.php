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

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Entity\ShipmentInterface;

final class AdjustmentSpec extends ObjectBehavior
{
    function it_implements_an_adjustment_interface(): void
    {
        $this->shouldImplement(AdjustmentInterface::class);
    }

    function its_details_are_mutable(): void
    {
        $this->setDetails(['taxRateAmount' => 0.1]);
        $this->getDetails()->shouldReturn(['taxRateAmount' => 0.1]);
    }

    function it_allows_assigning_itself_to_a_shipment(ShipmentInterface $shipment, OrderInterface $order): void
    {
        $shipment->getOrder()->willReturn($order);

        $this->setShipment($shipment);

        $this->getShipment()->shouldReturn($shipment);
    }
}

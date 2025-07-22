<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit\Model;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class ShipmentRefundTest extends TestCase
{
    /** @test */
    function it_implements_unit_refund_interface(): void
    {
        $shipmentRefund = new ShipmentRefund(1, 1000);
        
        $this->assertInstanceOf(UnitRefundInterface::class, $shipmentRefund);
    }

    /** @test */
    function it_has_id(): void
    {
        $shipmentRefund = new ShipmentRefund(1, 1000);
        
        $this->assertEquals(1, $shipmentRefund->id());
    }

    /** @test */
    function it_has_total(): void
    {
        $shipmentRefund = new ShipmentRefund(1, 1000);
        
        $this->assertEquals(1000, $shipmentRefund->total());
    }

    /** @test */
    function it_has_type(): void
    {
        $shipmentRefund = new ShipmentRefund(1, 1000);
        
        $this->assertEquals(RefundType::shipment(), $shipmentRefund->type());
    }
}
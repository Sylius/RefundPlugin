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
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class OrderItemUnitRefundTest extends TestCase
{
    /** @test */
    function it_implements_unit_refund_interface(): void
    {
        $orderItemUnitRefund = new OrderItemUnitRefund(1, 1000);
        
        $this->assertInstanceOf(UnitRefundInterface::class, $orderItemUnitRefund);
    }

    /** @test */
    function it_has_id(): void
    {
        $orderItemUnitRefund = new OrderItemUnitRefund(1, 1000);
        
        $this->assertEquals(1, $orderItemUnitRefund->id());
    }

    /** @test */
    function it_has_total(): void
    {
        $orderItemUnitRefund = new OrderItemUnitRefund(1, 1000);
        
        $this->assertEquals(1000, $orderItemUnitRefund->total());
    }

    /** @test */
    function it_has_type(): void
    {
        $orderItemUnitRefund = new OrderItemUnitRefund(1, 1000);
        
        $this->assertEquals(RefundType::orderItemUnit(), $orderItemUnitRefund->type());
    }
}
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

namespace Tests\Sylius\RefundPlugin\Unit\Event;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class UnitsRefundedTest extends TestCase
{
    /** @test */
    public function it_represents_an_immutable_fact_that_units_and_shipments_has_been_refunded(): void
    {
        $unitsRefunds = [
            new OrderItemUnitRefund(1, 1000),
            new ShipmentRefund(3, 500),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(4, 1000),
        ];

        $unitsRefunded = new UnitsRefunded('000222', $unitsRefunds, 1, 5000, 'USD', 'Comment');

        self::assertEquals('000222', $unitsRefunded->orderNumber());
        self::assertEquals($unitsRefunds, $unitsRefunded->units());
        self::assertEquals(1, $unitsRefunded->paymentMethodId());
        self::assertEquals(5000, $unitsRefunded->amount());
        self::assertEquals('USD', $unitsRefunded->currencyCode());
        self::assertEquals('Comment', $unitsRefunded->comment());
    }
}

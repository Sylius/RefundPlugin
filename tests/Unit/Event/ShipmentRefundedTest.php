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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Event\ShipmentRefunded;

final class ShipmentRefundedTest extends TestCase
{
    #[Test]
    public function it_represents_an_immutable_fact_that_shipment_has_been_refunded(): void
    {
        $shipmentRefunded = new ShipmentRefunded('000222', 1, 1000);

        self::assertEquals('000222', $shipmentRefunded->orderNumber());
        self::assertEquals(1, $shipmentRefunded->shipmentUnitId());
        self::assertEquals(1000, $shipmentRefunded->amount());
    }
}

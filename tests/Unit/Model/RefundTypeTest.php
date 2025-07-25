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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_order_item_unit_type(): void
    {
        $refundType = RefundType::orderItemUnit();

        self::assertEquals(RefundType::ORDER_ITEM_UNIT, $refundType->getValue());
    }

    #[Test]
    public function it_can_be_shipment_type(): void
    {
        $refundType = RefundType::shipment();

        self::assertEquals(RefundType::SHIPMENT, $refundType->getValue());
    }
}

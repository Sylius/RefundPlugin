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
use Sylius\RefundPlugin\Event\UnitRefunded;

final class UnitRefundedTest extends TestCase
{
    /** @test */
    function it_represents_an_immutable_fact_that_unit_has_been_refunded(): void
    {
        $unitRefunded = new UnitRefunded('000222', 1, 1000);

        self::assertEquals('000222', $unitRefunded->orderNumber());
        self::assertEquals(1, $unitRefunded->unitId());
        self::assertEquals(1000, $unitRefunded->amount());
    }
}

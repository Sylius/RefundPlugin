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

namespace Tests\Sylius\RefundPlugin\Unit\Command;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class RefundUnitsTest extends TestCase
{
    /** @test */
    public function it_represents_an_intention_to_refund_specific_units(): void
    {
        $orderItemUnit = $this->createMock(UnitRefundInterface::class);
        $shipmentUnit = $this->createMock(UnitRefundInterface::class);
        $unitRefunds = [$orderItemUnit, $shipmentUnit];

        $command = new RefundUnits('000222', $unitRefunds, 1, 'Comment');

        self::assertEquals('000222', $command->orderNumber());
        self::assertEquals($unitRefunds, $command->units());
        self::assertEquals(1, $command->paymentMethodId());
        self::assertEquals('Comment', $command->comment());
    }

    /** @test */
    public function it_throws_an_exception_if_units_are_not_an_instance_of_unit_refund_interface(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new RefundUnits('000222', [new \stdClass()], 1, 'Comment');
    }
}

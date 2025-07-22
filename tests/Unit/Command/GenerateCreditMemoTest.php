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
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class GenerateCreditMemoTest extends TestCase
{
    /** @test */
    function it_represents_an_intention_to_generate_credit_memo(): void
    {
        $unitRefunds = [
            new OrderItemUnitRefund(1, 1000),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(1, 1000),
        ];

        $command = new GenerateCreditMemo('000222', 1000, $unitRefunds, 'Comment');

        self::assertEquals('000222', $command->orderNumber());
        self::assertEquals(1000, $command->total());
        self::assertEquals($unitRefunds, $command->units());
        self::assertEquals('Comment', $command->comment());
    }
}

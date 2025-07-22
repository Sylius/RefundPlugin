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
use Sylius\RefundPlugin\Event\CreditMemoGenerated;

final class CreditMemoGeneratedTest extends TestCase
{
    /** @test */
    function it_represents_an_immutable_fact_that_credit_memo_has_been_generated(): void
    {
        $creditMemoGenerated = new CreditMemoGenerated('2018/01/000001', '000222');

        $this->assertEquals('2018/01/000001', $creditMemoGenerated->number());
        $this->assertEquals('000222', $creditMemoGenerated->orderNumber());
    }
}
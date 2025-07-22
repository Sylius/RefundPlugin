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
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;

final class RefundPaymentGeneratedTest extends TestCase
{
    /** @test */
    public function it_represents_an_immutable_fact_that_refund_payment_has_been_generated(): void
    {
        $refundPaymentGenerated = new RefundPaymentGenerated(1, '000222', 10000, 'GBP', 2, 3);

        self::assertEquals(1, $refundPaymentGenerated->id());
        self::assertEquals('000222', $refundPaymentGenerated->orderNumber());
        self::assertEquals(10000, $refundPaymentGenerated->amount());
        self::assertEquals('GBP', $refundPaymentGenerated->currencyCode());
        self::assertEquals(2, $refundPaymentGenerated->paymentMethodId());
        self::assertEquals(3, $refundPaymentGenerated->paymentId());
    }
}

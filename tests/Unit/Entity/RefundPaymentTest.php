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

namespace Tests\Sylius\RefundPlugin\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentTest extends TestCase
{
    private RefundPayment $refundPayment;
    private OrderInterface $order;
    private PaymentMethodInterface $paymentMethod;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $this->refundPayment = new RefundPayment($this->order, 100, 'USD', RefundPaymentInterface::STATE_NEW, $this->paymentMethod);
    }

    /** @test */
    function it_is_initializable(): void
    {
        $this->assertInstanceOf(RefundPayment::class, $this->refundPayment);
    }

    /** @test */
    function it_implements_refund_payment_interface(): void
    {
        $this->assertInstanceOf(RefundPaymentInterface::class, $this->refundPayment);
    }

    /** @test */
    function it_has_no_id_by_default(): void
    {
        $this->assertNull($this->refundPayment->getId());
    }

    /** @test */
    function it_has_an_order(): void
    {
        $this->order->method('getNumber')->willReturn('000002');

        $this->assertSame($this->order, $this->refundPayment->getOrder());
    }

    /** @test */
    function it_has_amount(): void
    {
        $this->assertEquals(100, $this->refundPayment->getAmount());
    }

    /** @test */
    function it_has_currency_code(): void
    {
        $this->assertEquals('USD', $this->refundPayment->getCurrencyCode());
    }

    /** @test */
    function it_has_state(): void
    {
        $this->assertEquals(RefundPaymentInterface::STATE_NEW, $this->refundPayment->getState());
    }

    /** @test */
    function it_has_payment_method(): void
    {
        $this->assertInstanceOf(PaymentMethodInterface::class, $this->refundPayment->getPaymentMethod());
    }
}
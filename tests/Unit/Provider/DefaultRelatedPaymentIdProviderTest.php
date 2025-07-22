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

namespace Tests\Sylius\RefundPlugin\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Exception\CompletedPaymentNotFound;
use Sylius\RefundPlugin\Provider\DefaultRelatedPaymentIdProvider;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;

final class DefaultRelatedPaymentIdProviderTest extends TestCase
{
    private DefaultRelatedPaymentIdProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new DefaultRelatedPaymentIdProvider();
    }

    /** @test */
    function it_is_initializable(): void
    {
        $this->assertInstanceOf(DefaultRelatedPaymentIdProvider::class, $this->provider);
    }

    /** @test */
    function it_implements_related_payment_id_provider_interface(): void
    {
        $this->assertInstanceOf(RelatedPaymentIdProviderInterface::class, $this->provider);
    }

    /** @test */
    function it_provides_id_of_last_completed_payment_from_refund_payment_order(): void
    {
        $refundPayment = $this->createMock(RefundPaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $payment = $this->createMock(PaymentInterface::class);

        $refundPayment
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        $order
            ->expects($this->once())
            ->method('getLastPayment')
            ->with(PaymentInterface::STATE_COMPLETED)
            ->willReturn($payment);

        $payment
            ->expects($this->once())
            ->method('getId')
            ->willReturn(4);

        $result = $this->provider->getForRefundPayment($refundPayment);

        $this->assertSame(4, $result);
    }

    /** @test */
    function it_throws_exception_if_order_has_no_completed_payments(): void
    {
        $refundPayment = $this->createMock(RefundPaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $refundPayment
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        $order
            ->expects($this->once())
            ->method('getNumber')
            ->willReturn('000666');

        $order
            ->expects($this->once())
            ->method('getLastPayment')
            ->with(PaymentInterface::STATE_COMPLETED)
            ->willReturn(null);

        $this->expectException(CompletedPaymentNotFound::class);
        $this->expectExceptionMessage('000666');

        $this->provider->getForRefundPayment($refundPayment);
    }
}
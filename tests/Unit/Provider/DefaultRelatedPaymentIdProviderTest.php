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

use PHPUnit\Framework\Attributes\Test;
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
        parent::setUp();
        $this->provider = new DefaultRelatedPaymentIdProvider();
    }

    #[Test]
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(DefaultRelatedPaymentIdProvider::class, $this->provider);
    }

    #[Test]
    public function it_implements_related_payment_id_provider_interface(): void
    {
        self::assertInstanceOf(RelatedPaymentIdProviderInterface::class, $this->provider);
    }

    #[Test]
    public function it_provides_id_of_last_completed_payment_from_refund_payment_order(): void
    {
        $refundPayment = $this->createMock(RefundPaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $payment = $this->createMock(PaymentInterface::class);

        $refundPayment
            ->expects(self::once())
            ->method('getOrder')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getLastPayment')
            ->with(PaymentInterface::STATE_COMPLETED)
            ->willReturn($payment);

        $payment
            ->expects(self::once())
            ->method('getId')
            ->willReturn(4);

        $result = $this->provider->getForRefundPayment($refundPayment);

        self::assertSame(4, $result);
    }

    #[Test]
    public function it_throws_exception_if_order_has_no_completed_payments(): void
    {
        $refundPayment = $this->createMock(RefundPaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $refundPayment
            ->expects(self::once())
            ->method('getOrder')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getNumber')
            ->willReturn('000666');

        $order
            ->expects(self::once())
            ->method('getLastPayment')
            ->with(PaymentInterface::STATE_COMPLETED)
            ->willReturn(null);

        $this->expectException(CompletedPaymentNotFound::class);
        $this->expectExceptionMessage('000666');

        $this->provider->getForRefundPayment($refundPayment);
    }
}

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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\RefundPlugin\Provider\RefundPaymentMethodsProviderInterface;
use Sylius\RefundPlugin\Provider\SupportedRefundPaymentMethodsProvider;

final class SupportedRefundPaymentMethodsProviderTest extends TestCase
{
    private PaymentMethodRepositoryInterface $paymentMethodRepository;
    private SupportedRefundPaymentMethodsProvider $provider;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->provider = new SupportedRefundPaymentMethodsProvider($this->paymentMethodRepository, ['offline', 'stripe']);
    }

    /** @test */
    function it_is_initializable(): void
    {
        $this->assertInstanceOf(SupportedRefundPaymentMethodsProvider::class, $this->provider);
    }

    /** @test */
    function it_implements_refund_payment_methods_provider_interface(): void
    {
        $this->assertInstanceOf(RefundPaymentMethodsProviderInterface::class, $this->provider);
    }

    /** @test */
    function it_provides_only_supported_payment_methods(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $offlinePaymentMethod = $this->createMock(PaymentMethodInterface::class);
        $payPalPaymentMethod = $this->createMock(PaymentMethodInterface::class);
        $stripePaymentMethod = $this->createMock(PaymentMethodInterface::class);
        $offlineGatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $payPalGatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $stripeGatewayConfig = $this->createMock(GatewayConfigInterface::class);

        $order
            ->expects($this->once())
            ->method('getChannel')
            ->willReturn($channel);

        $this->paymentMethodRepository
            ->expects($this->once())
            ->method('findEnabledForChannel')
            ->with($channel)
            ->willReturn([
                $offlinePaymentMethod,
                $payPalPaymentMethod,
                $stripePaymentMethod,
            ]);

        $offlinePaymentMethod
            ->expects($this->once())
            ->method('getGatewayConfig')
            ->willReturn($offlineGatewayConfig);

        $offlineGatewayConfig
            ->expects($this->once())
            ->method('getFactoryName')
            ->willReturn('offline');

        $payPalPaymentMethod
            ->expects($this->once())
            ->method('getGatewayConfig')
            ->willReturn($payPalGatewayConfig);

        $payPalGatewayConfig
            ->expects($this->once())
            ->method('getFactoryName')
            ->willReturn('paypal');

        $stripePaymentMethod
            ->expects($this->once())
            ->method('getGatewayConfig')
            ->willReturn($stripeGatewayConfig);

        $stripeGatewayConfig
            ->expects($this->once())
            ->method('getFactoryName')
            ->willReturn('stripe');

        $result = $this->provider->findForOrder($order);

        $this->assertSame([$offlinePaymentMethod, $stripePaymentMethod], $result);
    }
}
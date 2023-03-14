<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use Payum\Core\Model\GatewayConfigInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\RefundPlugin\Provider\RefundPaymentMethodsProviderInterface;

final class SupportedRefundPaymentMethodsProviderSpec extends ObjectBehavior
{
    function let(PaymentMethodRepositoryInterface $paymentMethodRepository): void
    {
        $this->beConstructedWith($paymentMethodRepository, ['offline', 'stripe']);
    }

    function it_implements_refund_payment_methods_provider_interface(): void
    {
        $this->shouldImplement(RefundPaymentMethodsProviderInterface::class);
    }

    function it_provides_only_supported_payment_methods(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ChannelInterface $channel,
        PaymentMethodInterface $offlinePaymentMethod,
        PaymentMethodInterface $payPalPaymentMethod,
        PaymentMethodInterface $stripePaymentMethod,
        GatewayConfigInterface $offlineGatewayConfig,
        GatewayConfigInterface $payPalGatewayConfig,
        GatewayConfigInterface $stripeGatewayConfig,
    ): void {
        $paymentMethodRepository->findEnabledForChannel($channel)->willReturn([
            $offlinePaymentMethod,
            $payPalPaymentMethod,
            $stripePaymentMethod,
        ]);

        $offlinePaymentMethod->getGatewayConfig()->willReturn($offlineGatewayConfig);
        $offlineGatewayConfig->getFactoryName()->willReturn('offline');

        $payPalPaymentMethod->getGatewayConfig()->willReturn($payPalGatewayConfig);
        $payPalGatewayConfig->getFactoryName()->willReturn('paypal');

        $stripePaymentMethod->getGatewayConfig()->willReturn($stripeGatewayConfig);
        $stripeGatewayConfig->getFactoryName()->willReturn('stripe');

        $this->findForChannel($channel)->shouldReturn([$offlinePaymentMethod, $stripePaymentMethod]);
    }
}

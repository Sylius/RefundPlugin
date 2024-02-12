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

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Webmozart\Assert\Assert;

final class SupportedRefundPaymentMethodsProvider implements RefundPaymentMethodsProviderInterface
{
    private PaymentMethodRepositoryInterface $paymentMethodRepository;

    private array $supportedGateways;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository, array $supportedGateways)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->supportedGateways = $supportedGateways;
    }

    public function findForChannel(ChannelInterface $channel): array
    {
        trigger_deprecation('sylius/refund-plugin', '1.4', 'The "%s::findForChannel" method is deprecated and will be removed in 2.0. Use "%s::findForOrder" instead.', self::class, self::class);

        return $this->find($channel);
    }

    public function findForOrder(OrderInterface $order): array
    {
        /** @var ChannelInterface|null $channel */
        $channel = $order->getChannel();
        Assert::notNull($channel);

        return $this->find($channel);
    }

    private function find(ChannelInterface $channel): array
    {
        return array_values(array_filter(
            $this->paymentMethodRepository->findEnabledForChannel($channel),
            function (PaymentMethodInterface $paymentMethod): bool {
                $gatewayConfig = $paymentMethod->getGatewayConfig();
                Assert::notNull($gatewayConfig);

                return in_array($gatewayConfig->getFactoryName(), $this->supportedGateways, true);
            },
        ));
    }
}

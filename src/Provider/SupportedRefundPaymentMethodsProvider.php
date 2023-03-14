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

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
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

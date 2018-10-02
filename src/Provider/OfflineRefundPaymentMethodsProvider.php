<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;

final class OfflineRefundPaymentMethodsProvider implements RefundPaymentMethodsProviderInterface
{
    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function findForChannel(ChannelInterface $channel): array
    {
        $paymentMethods = $this->paymentMethodRepository->findEnabledForChannel($channel);

        /** @var PaymentMethodInterface $paymentMethod */
        foreach ($paymentMethods as $key => $paymentMethod) {
            if ($paymentMethod->getGatewayConfig()->getFactoryName() !== 'offline') {
                unset($paymentMethods[$key]);
            }
        }

        return $paymentMethods;
    }
}

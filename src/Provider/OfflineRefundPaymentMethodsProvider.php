<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Webmozart\Assert\Assert;

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
        return array_filter(
            $this->paymentMethodRepository->findEnabledForChannel($channel),
            function (PaymentMethodInterface $paymentMethod): bool {
                Assert::notNull($paymentMethod->getGatewayConfig());

                return $paymentMethod->getGatewayConfig()->getFactoryName() === 'offline';
            }
        );
    }
}

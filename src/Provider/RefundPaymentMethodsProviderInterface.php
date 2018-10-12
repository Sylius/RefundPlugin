<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface RefundPaymentMethodsProviderInterface
{
    /** @return array|PaymentMethodInterface[] */
    public function findForChannel(ChannelInterface $channel): array;
}

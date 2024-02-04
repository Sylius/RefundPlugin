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

/**
 * @method PaymentMethodInterface[] findForOrder(OrderInterface $order)
 */
interface RefundPaymentMethodsProviderInterface
{
    /**
     * @deprecated since 1.4, to be removed in 2.0, use findForOrder() instead
     *
     * @return PaymentMethodInterface[]
     */
    public function findForChannel(ChannelInterface $channel): array;
}

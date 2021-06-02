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

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RefundPaymentFactoryInterface
{
    public function createWithData(
        string $orderNumber,
        int $amount,
        string $currencyCode,
        string $state,
        int $paymentMethodId
    ): RefundPaymentInterface;
}

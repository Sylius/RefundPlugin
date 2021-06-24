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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentFactory implements RefundPaymentFactoryInterface
{
    /** @var string */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createWithData(
        OrderInterface $order,
        int $amount,
        string $currencyCode,
        string $state,
        PaymentMethodInterface $paymentMethod
    ): RefundPaymentInterface {
        return new $this->className($order, $amount, $currencyCode, $state, $paymentMethod);
    }
}

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
use Webmozart\Assert\Assert;

final class RefundPaymentFactory implements RefundPaymentFactoryInterface
{
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createNew(): RefundPaymentInterface
    {
        throw new \InvalidArgumentException('Default creation method is forbidden for this object. Use `createWithData` instead.');
    }

    public function createWithData(
        OrderInterface $order,
        int $amount,
        string $currencyCode,
        string $state,
        PaymentMethodInterface $paymentMethod,
    ): RefundPaymentInterface {
        $refundPayment = new $this->className($order, $amount, $currencyCode, $state, $paymentMethod);
        Assert::isInstanceOf($refundPayment, RefundPaymentInterface::class);

        return $refundPayment;
    }
}

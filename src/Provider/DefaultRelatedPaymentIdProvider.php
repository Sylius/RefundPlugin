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

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Exception\CompletedPaymentNotFound;

final class DefaultRelatedPaymentIdProvider implements RelatedPaymentIdProviderInterface
{
    public function getForRefundPayment(RefundPaymentInterface $refundPayment): int
    {
        $order = $refundPayment->getOrder();
        $payment = $order->getLastPayment(PaymentInterface::STATE_COMPLETED);

        if ($payment === null) {
            throw CompletedPaymentNotFound::withNumber((string) $order->getNumber());
        }

        return $payment->getId();
    }
}

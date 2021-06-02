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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Exception\CompletedPaymentNotFound;
use Sylius\RefundPlugin\Exception\OrderNotFound;

final class DefaultRelatedPaymentIdProvider implements RelatedPaymentIdProviderInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getForRefundPayment(RefundPaymentInterface $refundPayment): int
    {
        $orderNumber = $refundPayment->getOrderNumber();
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        if ($order === null) {
            throw OrderNotFound::withNumber($orderNumber);
        }

        $payment = $order->getLastPayment(PaymentInterface::STATE_COMPLETED);

        if ($payment === null) {
            throw CompletedPaymentNotFound::withNumber($orderNumber);
        }

        return $payment->getId();
    }
}

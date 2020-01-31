<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class OrderRefundsListAvailabilityChecker implements OrderRefundingAvailabilityCheckerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(string $orderNumber): bool
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        return
            in_array($order->getPaymentState(), [
                OrderPaymentStates::STATE_PAID,
                OrderPaymentStates::STATE_PARTIALLY_REFUNDED,
                OrderPaymentStates::STATE_REFUNDED,
            ], true) &&
            $order->getTotal() !== 0
        ;
    }
}

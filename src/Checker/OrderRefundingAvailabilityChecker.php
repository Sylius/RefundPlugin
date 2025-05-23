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

namespace Sylius\RefundPlugin\Checker;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final readonly class OrderRefundingAvailabilityChecker implements OrderRefundingAvailabilityCheckerInterface
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ?StateMachineInterface $stateMachine = null,
    ) {
        if (null === $this->stateMachine) {
            trigger_deprecation(
                'sylius/refund-plugin',
                '2.0',
                'Not passing an $stateMachine to %s constructor is deprecated and will be prohibited in SyliusRefund 3.0.',
                self::class,
            );
        }
    }

    public function __invoke(string $orderNumber): bool
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        if (null !== $this->stateMachine) {
            return
                (
                    $this->stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND) ||
                    $this->stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)
                ) &&
                $order->getTotal() !== 0
            ;
        }

        return
            in_array($order->getPaymentState(), [
                OrderPaymentStates::STATE_PAID,
                OrderPaymentStates::STATE_PARTIALLY_REFUNDED,
            ], true) &&
            $order->getTotal() !== 0
        ;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderFullyRefundedStateResolver implements OrderFullyRefundedStateResolverInterface
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var ObjectManager */
    private $orderManager;

    public function __construct(FactoryInterface $stateMachineFactory, ObjectManager $orderManager)
    {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderManager = $orderManager;
    }

    public function resolve(OrderInterface $order): void
    {
        if (OrderStates::STATE_FULLY_REFUNDED === $order->getState()) {
            return;
        }

        $stateMachine = $this->stateMachineFactory->get($order, OrderTransitions::GRAPH);

        $stateMachine->apply(OrderTransitions::TRANSITION_REFUND);

        $this->orderManager->flush();
    }
}

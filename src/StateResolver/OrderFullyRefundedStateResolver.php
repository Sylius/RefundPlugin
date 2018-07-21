<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderFullyRefundedStateResolver implements OrderFullyRefundedStateResolverInterface
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(FactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function resolve(OrderInterface $order): void
    {
        $stateMachine = $this->stateMachineFactory->get($order,OrderTransitions::GRAPH);

        $stateMachine->apply(OrderTransitions::REFUND);
    }
}

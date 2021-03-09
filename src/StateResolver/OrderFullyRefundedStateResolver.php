<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

use Doctrine\Persistence\ObjectManager;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;
use Webmozart\Assert\Assert;

final class OrderFullyRefundedStateResolver implements OrderFullyRefundedStateResolverInterface
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var ObjectManager */
    private $orderManager;

    /** @var OrderFullyRefundedTotalCheckerInterface */
    private $orderFullyRefundedTotalChecker;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        ObjectManager $orderManager,
        OrderFullyRefundedTotalCheckerInterface $orderFullyRefundedTotalChecker,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderManager = $orderManager;
        $this->orderFullyRefundedTotalChecker = $orderFullyRefundedTotalChecker;
        $this->orderRepository = $orderRepository;
    }

    public function resolve(string $orderNumber): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        if (
            !$this->orderFullyRefundedTotalChecker->isOrderFullyRefunded($order) ||
            OrderPaymentStates::STATE_REFUNDED === $order->getPaymentState()
        ) {
            return;
        }

        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);

        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REFUND);

        $this->orderManager->flush();
    }
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Exception\OrderNotFound;

final class UnitRefundedEventListener
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var ObjectManager */
    private $orderManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $stateMachineFactory,
        ObjectManager $orderManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderManager = $orderManager;
    }

    public function __invoke(UnitRefunded $unitRefunded): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($unitRefunded->orderNumber());
        if ($order === null) {
            throw OrderNotFound::withNumber($unitRefunded->orderNumber());
        }

        if ($order->getPaymentState() === OrderPaymentStates::STATE_PARTIALLY_REFUNDED) {
            return;
        }

        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND);

        $this->orderManager->flush();
    }
}

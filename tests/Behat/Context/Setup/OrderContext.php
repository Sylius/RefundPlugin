<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\OrderPaymentTransitions;

final readonly class OrderContext implements Context
{
    public function __construct(
        private EntityManagerInterface $orderManager,
        private SharedStorageInterface $sharedStorage,
        private StateMachineInterface $stateMachine,
    ) {
    }

    /**
     * @Given /^(this order) has been placed in ("[^"]+" channel)$/
     */
    public function orderHasBeenPlacedInChannel(OrderInterface $order, ChannelInterface $channel): void
    {
        $order->setChannel($channel);

        $this->orderManager->flush();
    }

    #[Given('/^the customer completed the checkout with ("[^"]+" shipping method)$/')]
    public function theCustomerCompletedTheCheckoutWithShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
        if (!$order->getPayments()->isEmpty()) {
            $this->stateMachine->apply($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY);
        }

        $this->orderManager->flush();
    }

    private function applyTransitionOnOrderCheckout(OrderInterface $order, string $transition): void
    {
        $this->stateMachine->apply($order, OrderCheckoutTransitions::GRAPH, $transition);
    }
}

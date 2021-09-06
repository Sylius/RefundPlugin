<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Application\Kernel;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;

if (Kernel::MAJOR_VERSION === '1' && Kernel::MINOR_VERSION === '8') {
    final class OrderShipmentContext implements Context
    {
        private SharedStorageInterface $sharedStorage;

        private \Sylius\Component\Resource\Factory\FactoryInterface $orderItemFactory;

        private \Sylius\Component\Resource\Factory\FactoryInterface $shipmentFactory;

        private StateMachineFactoryInterface $stateMachineFactory;

        private OrderRepositoryInterface $orderRepository;

        private PaymentMethodRepositoryInterface $paymentMethodRepository;

        private ShippingMethodRepositoryInterface $shippingMethodRepository;

        private ProductVariantResolverInterface $variantResolver;

        private OrderItemQuantityModifierInterface $itemQuantityModifier;

        private ObjectManager $objectManager;

        public function __construct(
            SharedStorageInterface $sharedStorage,
            FactoryInterface $orderItemFactory,
            FactoryInterface $shipmentFactory,
            StateMachineFactoryInterface $stateMachineFactory,
            OrderRepositoryInterface $orderRepository,
            PaymentMethodRepositoryInterface $paymentMethodRepository,
            ShippingMethodRepositoryInterface $shippingMethodRepository,
            ProductVariantResolverInterface $variantResolver,
            OrderItemQuantityModifierInterface $itemQuantityModifier,
            ObjectManager $objectManager
        ) {
            $this->sharedStorage = $sharedStorage;
            $this->orderItemFactory = $orderItemFactory;
            $this->shipmentFactory = $shipmentFactory;
            $this->stateMachineFactory = $stateMachineFactory;
            $this->orderRepository = $orderRepository;
            $this->paymentMethodRepository = $paymentMethodRepository;
            $this->shippingMethodRepository = $shippingMethodRepository;
            $this->variantResolver = $variantResolver;
            $this->itemQuantityModifier = $itemQuantityModifier;
            $this->objectManager = $objectManager;
        }

        /**
         * @Given the customer bought another :product with separate :shippingMethod shipment
         */
        public function theCustomerBoughtAnotherProductWithSeparateShipment(
            ProductInterface $product,
            ShippingMethodInterface $shippingMethod
        ): void {
            $this->addProductVariantToOrder($this->variantResolver->getVariant($product), 1);

            /** @var OrderInterface $order */
            $order = $this->sharedStorage->get('order');

            /** @var ShipmentInterface $shipment */
            $shipment = $this->shipmentFactory->createNew();
            $shipment->setMethod($shippingMethod);
            $shipment->setOrder($order);
            $order->addShipment($shipment);

            $this->objectManager->flush();
        }

        /**
         * @Given /^the customer chose ("[^"]+" shipping method) (to "[^"]+")$/
         */
        public function theCustomerChoseShippingTo(ShippingMethodInterface $shippingMethod, AddressInterface $address): void
        {
            /** @var OrderInterface $order */
            $order = $this->sharedStorage->get('order');

            $order->setShippingAddress($address);
            $order->setBillingAddress(clone $address);

            $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);

            foreach ($order->getShipments() as $shipment) {
                $shipment->setMethod($shippingMethod);
            }
            $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

            $this->objectManager->flush();
        }

        private function addProductVariantToOrder(
            ProductVariantInterface $productVariant,
            int $quantity = 1,
            ?ChannelInterface $channel = null
        ): OrderInterface {
            $order = $this->sharedStorage->get('order');

            $this->addProductVariantsToOrderWithChannelPrice(
                $order,
                $channel ?? $this->sharedStorage->get('channel'),
                $productVariant,
                (int) $quantity
            );

            return $order;
        }

        private function addProductVariantsToOrderWithChannelPrice(
            OrderInterface $order,
            ChannelInterface $channel,
            ProductVariantInterface $productVariant,
            int $quantity = 1
        ) {
            /** @var OrderItemInterface $item */
            $item = $this->orderItemFactory->createNew();
            $item->setVariant($productVariant);

            /** @var ChannelPricingInterface $channelPricing */
            $channelPricing = $productVariant->getChannelPricingForChannel($channel);
            $item->setUnitPrice($channelPricing->getPrice());

            $this->itemQuantityModifier->modify($item, $quantity);

            $order->addItem($item);
        }

        private function applyTransitionOnOrderCheckout(OrderInterface $order, string $transition): void
        {
            $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->apply($transition);
        }
    }
} else {
    final class OrderShipmentContext implements Context
    {
    }
}

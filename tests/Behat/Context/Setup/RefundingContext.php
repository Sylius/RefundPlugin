<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Entity\ShipmentInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class RefundingContext implements Context
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(OrderRepositoryInterface $orderRepository, MessageBusInterface $commandBus)
    {
        $this->orderRepository = $orderRepository;
        $this->commandBus = $commandBus;
    }

    /**
     * @Given /^(\d)(?:|st|nd|rd) "([^"]+)" product from order "#([^"]+)" has already been refunded with ("[^"]+" payment)$/
     * @Given :productName product from order :orderNumber has already been refunded with :paymentMethod payment
     */
    public function productFromOrderHasAlreadyBeenRefunded(
        ?int $unitNumber,
        string $productName,
        string $orderNumber,
        PaymentMethodInterface $paymentMethod
    ): void {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $unitsWithProduct = $this->getUnitsWithProduct($order, $productName);
        /** @var OrderItemUnitInterface $unit */
        $unit = $unitsWithProduct[($unitNumber ?? 1)-1];

        $this->commandBus->dispatch(new RefundUnits(
            $orderNumber,
            [new OrderItemUnitRefund($unit->getId(), $unit->getTotal())],
            [],
            $paymentMethod->getId(),
            ''
        ));
    }

    /**
     * @Given /^the (\d)(?:|st|nd|rd) "([^"]+)" product from order "#([^"]+)" has a refund of ("[^"]+") with ("[^"]+" payment)$/
     */
    public function theProductFromOrderHasARefundOfWith(
        int $unitNumber,
        string $productName,
        string $orderNumber,
        int $partialTotal,
        PaymentMethodInterface $paymentMethod
    ): void {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $unitsWithProduct = $this->getUnitsWithProduct($order, $productName);
        /** @var OrderItemUnitInterface $unit */
        $unit = $unitsWithProduct[$unitNumber-1];

        $this->commandBus->dispatch(new RefundUnits(
            $orderNumber, [new OrderItemUnitRefund($unit->getId(), $partialTotal)], [], $paymentMethod->getId(), ''
        ));
    }

    /**
     * @Given /^all units from the order "#([^"]+)" are refunded with ("[^"]+" payment)$/
     */
    public function allUnitsFromOrderAreRefunded(
        string $orderNumber,
        PaymentMethodInterface $paymentMethod
    ): void {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $units = array_map(function(OrderItemUnitInterface $unit) {
            return new OrderItemUnitRefund($unit->getId(), $unit->getTotal());
        }, $order->getItemUnits()->getValues());

        $this->commandBus->dispatch(new RefundUnits(
            $orderNumber,
            $units,
            [],
            $paymentMethod->getId(),
            ''
        ));
    }

    /**
     * @Given /^all units and shipment from the order "#([^"]+)" are refunded with ("[^"]+" payment)$/
     * @Given /^all units and shipment from the order "#([^"]+)" have been refunded with ("[^"]+" payment)$/
     */
    public function allUnitsAndShipmentFromOrderAreRefunded(
        string $orderNumber,
        PaymentMethodInterface $paymentMethod
    ): void {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $units = array_map(function(OrderItemUnitInterface $unit) {
            return new OrderItemUnitRefund($unit->getId(), $unit->getTotal());
        }, $order->getItemUnits()->getValues());

        /** @var ShipmentInterface $shipment */
        $shipment = $order->getShipments()->first();
        $shippingAdjustment = $shipment->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->first();

        $this->commandBus->dispatch(new RefundUnits(
            $orderNumber,
            $units,
            [new ShipmentRefund($shippingAdjustment->getId(), $shipment->getAdjustmentsTotal())],
            $paymentMethod->getId(),
            ''
        ));
    }

    /**
     * @Given /^the "#([^"]+)" order's shipping cost already has a refund of ("[^"]+") with ("[^"]+" payment)$/
     */
    public function shipmentFromOrderHasAlreadyRefundedWithPayment(
        string $orderNumber,
        int $amount,
        PaymentMethodInterface $paymentMethod
    ) {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $shipment = $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->first();

        $this->commandBus->dispatch(new RefundUnits(
            $orderNumber,
            [],
            [new ShipmentRefund($shipment->getId(), $amount)],
            $paymentMethod->getId(),
            ''
        ));
    }

    private function getUnitsWithProduct(OrderInterface $order, string $productName): array
    {
        $unitsWithProduct = $order->getItemUnits()->filter(function(OrderItemUnitInterface $unit) use ($productName): bool {
            return $unit->getOrderItem()->getProductName() === $productName;
        });

        return array_values($unitsWithProduct->toArray());
    }
}

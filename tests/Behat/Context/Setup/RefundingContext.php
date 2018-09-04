<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Prooph\ServiceBus\CommandBus;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefund;
use Webmozart\Assert\Assert;

final class RefundingContext implements Context
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(OrderRepositoryInterface $orderRepository, CommandBus $commandBus)
    {
        $this->orderRepository = $orderRepository;
        $this->commandBus = $commandBus;
    }

    /**
     * @Given /^(\d)(?:|st|nd|rd) "([^"]+)" product from order "#([^"]+)" has already been refunded with ("[^"]+" payment)$/
     */
    public function productFromOrderHasAlreadyBeenRefunded(
        int $unitNumber,
        string $productName,
        string $orderNumber,
        PaymentMethodInterface $paymentMethod
    ): void {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $unitsWithProduct = $this->getUnitsWithProduct($order, $productName);
        /** @var OrderItemUnitInterface $unit */
        $unit = $unitsWithProduct[$unitNumber-1];

        $this->commandBus->dispatch(new RefundUnits(
            $orderNumber,
            [new UnitRefund($unit->getId(), $unit->getTotal())],
            [],
            $paymentMethod->getId(),
            ''
        ));
    }

    /**
     * @Given /^(\d)(?:|st|nd|rd) "([^"]+)" product from order "#([^"]+)" has already ("[^"]+") refunded with ("[^"]+" payment)$/
     */
    public function partOfProductFromOrderHasAlreadyBeenRefunded(
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
            $orderNumber, [new UnitRefund($unit->getId(), $partialTotal)], [], $paymentMethod->getId(), ''
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
            return new UnitRefund($unit->getId(), $unit->getTotal());
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
     */
    public function allUnitsAndShipmentFromOrderAreRefunded(
        string $orderNumber,
        PaymentMethodInterface $paymentMethod
    ): void {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $units = array_map(function(OrderItemUnitInterface $unit) {
            return new UnitRefund($unit->getId(), $unit->getTotal());
        }, $order->getItemUnits()->getValues());

        $shipment = $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->first();

        $this->commandBus->dispatch(new RefundUnits(
            $orderNumber,
            $units,
            [$shipment->getId()],
            $paymentMethod->getId(),
            ''
        ));
    }

    /**
     * @Given /^shipment from order "#([^"]+)" has already ("[^"]+") refunded with ("[^"]+" payment)$/
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

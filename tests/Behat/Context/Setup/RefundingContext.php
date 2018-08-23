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

        $unitsWithProduct = $order->getItemUnits()->filter(function(OrderItemUnitInterface $unit) use ($productName): bool {
            return $unit->getOrderItem()->getProductName() === $productName;
        });
        $unitsWithProduct = array_values($unitsWithProduct->toArray());

        $unit = $unitsWithProduct[$unitNumber-1];

        $this->commandBus->dispatch(new RefundUnits($orderNumber, [$unit->getId()], [], $paymentMethod->getId(), ''));
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

        $orderItemUnits = array_map(function(OrderItemUnitInterface $unit) {
            return $unit->getId();
        }, $order->getItemUnits()->getValues());

        $shipment = $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->first();

        $this->commandBus->dispatch(new RefundUnits(
            $orderNumber,
            $orderItemUnits,
            [$shipment->getId()],
            $paymentMethod->getId(),
            ''
        ));
    }
}

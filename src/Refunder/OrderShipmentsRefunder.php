<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Prooph\ServiceBus\EventBus;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RefundedShipmentFeeProviderInterface;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class OrderShipmentsRefunder implements RefunderInterface
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var RefundedShipmentFeeProviderInterface */
    private $refundedShipmentFeeProvider;

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        RefundCreatorInterface $refundCreator,
        RefundedShipmentFeeProviderInterface $refundedShipmentFeeProvider,
        EventBus $eventBus
    ) {
        $this->refundCreator = $refundCreator;
        $this->refundedShipmentFeeProvider = $refundedShipmentFeeProvider;
        $this->eventBus = $eventBus;
    }

    public function refundFromOrder(array $unitIds, string $orderNumber): int
    {
        $refundedTotal = 0;
        foreach ($unitIds as $shipmentUnitId) {
            $refundAmount = $this->refundedShipmentFeeProvider->getFeeOfShipment($shipmentUnitId);

            $this->refundCreator->__invoke($orderNumber, $shipmentUnitId, $refundAmount, RefundType::shipment());

            $refundedTotal += $refundAmount;

            $this->eventBus->dispatch(new ShipmentRefunded($orderNumber, $shipmentUnitId, $refundAmount));
        }

        return $refundedTotal;
    }
}

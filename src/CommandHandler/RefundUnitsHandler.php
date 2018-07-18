<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Prooph\ServiceBus\EventBus;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;
use Sylius\RefundPlugin\Provider\RefundedShipmentFeeProviderInterface;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class RefundUnitsHandler
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var RefundedUnitTotalProviderInterface */
    private $refundedUnitTotalProvider;

    /** @var RefundedShipmentFeeProviderInterface */
    private $refundedShippingFeeProvider;

    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundingAvailabilityChecker;

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        RefundedShipmentFeeProviderInterface $refundedShippingFeeProvider,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        EventBus $eventBus
    ) {
        $this->refundCreator = $refundCreator;
        $this->refundedUnitTotalProvider = $refundedUnitTotalProvider;
        $this->refundedShippingFeeProvider = $refundedShippingFeeProvider;
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
        $this->eventBus = $eventBus;
    }

    public function __invoke(RefundUnits $command): void
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($command->orderNumber())) {
            throw OrderNotAvailableForRefundingException::withOrderNumber($command->orderNumber());
        }

        $refundedTotal = 0;
        foreach ($command->refundedUnitIds() as $refundedUnitId) {
            $refundAmount = $this->refundedUnitTotalProvider->getTotalOfUnitWithId($refundedUnitId);

            $this->refundCreator->__invoke($command->orderNumber(), $refundedUnitId, $refundAmount);

            $refundedTotal += $refundAmount;
        }

        foreach ($command->refundedShipmentIds() as $shipmentId) {
            $shippingRefundAmount = $this->refundedShippingFeeProvider->getFeeOfShipment($shipmentId);

            $this->refundCreator->__invoke($command->orderNumber(), $shipmentId, $shippingRefundAmount);

            $refundedTotal += $shippingRefundAmount;
        }

        $this->eventBus->dispatch(new UnitsRefunded(
            $command->orderNumber(),
            $command->refundedUnitIds(),
            $command->refundedShipmentIds(),
            $refundedTotal
        ));
    }
}

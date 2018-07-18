<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Prooph\ServiceBus\EventBus;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class OrderUnitsRefunder implements RefunderInterface
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var RefundedUnitTotalProviderInterface */
    private $refundedUnitTotalProvider;

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        EventBus $eventBus
    ) {
        $this->refundCreator = $refundCreator;
        $this->refundedUnitTotalProvider = $refundedUnitTotalProvider;
        $this->eventBus = $eventBus;
    }

    public function refundFromOrder(array $unitIds, string $orderNumber): int
    {
        $refundedTotal = 0;
        foreach ($unitIds as $unitId) {
            $refundAmount = $this->refundedUnitTotalProvider->getTotalOfUnitWithId($unitId);

            $this->refundCreator->__invoke($orderNumber, $unitId, $refundAmount);

            $refundedTotal += $refundAmount;

            $this->eventBus->dispatch(new UnitRefunded($orderNumber, $unitId, $refundAmount));
        }

        return $refundedTotal;
    }
}

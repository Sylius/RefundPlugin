<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Prooph\ServiceBus\EventBus;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;

final class OrderItemUnitsRefunder implements RefunderInterface
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        RefundCreatorInterface $refundCreator,
        EventBus $eventBus
    ) {
        $this->refundCreator = $refundCreator;
        $this->eventBus = $eventBus;
    }

    public function refundFromOrder(array $units, string $orderNumber): int
    {
        $refundedTotal = 0;

        /** @var OrderItemUnitRefund $unit */
        foreach ($units as $unit) {
            $this->refundCreator->__invoke($orderNumber, $unit->id(), $unit->total(), RefundType::orderItemUnit());

            $refundedTotal += $unit->total();

            $this->eventBus->dispatch(new UnitRefunded($orderNumber, $unit->id(), $unit->total()));
        }

        return $refundedTotal;
    }
}

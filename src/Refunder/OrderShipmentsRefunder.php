<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderShipmentsRefunder implements RefunderInterface
{
    public function __construct(
        private RefundCreatorInterface $refundCreator,
        private MessageBusInterface $eventBus,
        private UnitRefundFilterInterface $unitRefundFilter,
    ) {
    }

    public function refundFromOrder(array $units, string $orderNumber): int
    {
        $refundedTotal = 0;

        $units = $this->unitRefundFilter->filterUnitRefunds($units, ShipmentRefund::class);
        foreach ($units as $shipmentUnit) {
            $this->refundCreator->__invoke(
                $orderNumber,
                $shipmentUnit->id(),
                $shipmentUnit->total(),
                RefundType::shipment(),
            );

            $refundedTotal += $shipmentUnit->total();

            $this->eventBus->dispatch(new ShipmentRefunded($orderNumber, $shipmentUnit->id(), $shipmentUnit->total()));
        }

        return $refundedTotal;
    }
}

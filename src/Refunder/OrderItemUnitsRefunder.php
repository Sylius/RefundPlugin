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
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemUnitsRefunder implements RefunderInterface
{
    private RefundCreatorInterface $refundCreator;

    private MessageBusInterface $eventBus;

    public function __construct(
        RefundCreatorInterface $refundCreator,
        MessageBusInterface $eventBus,
    ) {
        $this->refundCreator = $refundCreator;
        $this->eventBus = $eventBus;
    }

    public function refundFromOrder(array $units, string $orderNumber): int
    {
        $units = array_filter($units, fn (UnitRefundInterface $unitRefund) => $unitRefund instanceof OrderItemUnitRefund);

        $refundedTotal = 0;

        /** @var UnitRefundInterface $unit */
        foreach ($units as $unit) {
            $this->refundCreator->__invoke(
                $orderNumber,
                $unit->id(),
                $unit->total(),
                $unit->type(),
            );

            $refundedTotal += $unit->total();

            $this->eventBus->dispatch(new UnitRefunded($orderNumber, $unit->id(), $unit->total()));
        }

        return $refundedTotal;
    }
}

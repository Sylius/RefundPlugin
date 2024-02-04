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
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemUnitsRefunder implements RefunderInterface
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

        $units = $this->unitRefundFilter->filterUnitRefunds($units, OrderItemUnitRefund::class);
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

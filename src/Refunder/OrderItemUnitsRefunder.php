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
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemUnitsRefunder implements RefunderInterface
{
    public function __construct(
        private RefundCreatorInterface $refundCreator,
        private MessageBusInterface $eventBus,
        private ?UnitRefundFilterInterface $unitRefundFilter = null,
    ) {
        if (null === $unitRefundFilter) {
            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Not passing a "%s" as a 3rd argument of "%s" constructor is deprecated and will be removed in 2.0.', UnitRefundFilterInterface::class, self::class));
        }
    }

    public function refundFromOrder(array $units, string $orderNumber): int
    {
        if (null === $this->unitRefundFilter) {
            $units = $this->filterOrderItemUnitRefunds($units);
        } else {
            $units = $this->unitRefundFilter->filterUnitRefunds($units, OrderItemUnitRefund::class);
        }

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

    private function filterOrderItemUnitRefunds(array $units): array
    {
        return array_filter($units, fn (UnitRefundInterface $unitRefund) => $unitRefund instanceof OrderItemUnitRefund);
    }
}

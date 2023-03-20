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

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Doctrine\ORM\UnitRefundBelongsToOrderQueryInterface;
use Sylius\RefundPlugin\Exception\RefundUnitsNotBelongToOrder;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class RefundUnitsBelongToOrderValidator implements RefundUnitsBelongToOrderValidatorInterface
{
    public function __construct(
        private UnitRefundBelongsToOrderQueryInterface $unitRefundBelongsToOrderQuery,
        private UnitRefundFilterInterface $unitRefundFilter,
    ) {
    }

    public function validateUnits(array $unitRefunds, string $orderNumber): void
    {
        $orderItemUnitsBelongToOrder = $this->unitRefundBelongsToOrderQuery
            ->orderItemUnitRefundsBelongToOrder(
                array_map(
                    fn (UnitRefundInterface $unitRefund) => $unitRefund->id(),
                    $this->unitRefundFilter->filterUnitRefunds($unitRefunds, OrderItemUnitRefund::class),
                ),
                $orderNumber,
            )
        ;

        $shipmentRefundsBelongToOrder = $this->unitRefundBelongsToOrderQuery
            ->shipmentRefundsBelongToOrder(
                array_map(
                    fn (UnitRefundInterface $unitRefund) => $unitRefund->id(),
                    $this->unitRefundFilter->filterUnitRefunds($unitRefunds, ShipmentRefund::class),
                ),
                $orderNumber,
            )
        ;

        if (!$orderItemUnitsBelongToOrder || !$shipmentRefundsBelongToOrder) {
            throw RefundUnitsNotBelongToOrder::withValidationConstraint(
                RefundUnitsValidationConstraintMessages::REFUND_UNITS_MUST_BELONG_TO_ORDER,
            );
        }
    }
}

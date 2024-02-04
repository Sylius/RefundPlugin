<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Doctrine\ORM\CountRefundsBelongingToOrderQueryInterface;
use Sylius\RefundPlugin\Exception\RefundUnitsNotBelongToOrder;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class OrderItemUnitRefundsBelongingToOrderValidator implements UnitRefundsBelongingToOrderValidatorInterface
{
    public function __construct(
        private UnitRefundFilterInterface $unitRefundFilter,
        private CountRefundsBelongingToOrderQueryInterface $countRefundsBelongingToOrderQuery,
    ) {
    }

    public function validateUnits(array $unitRefunds, string $orderNumber): void
    {
        $orderItemUnitRefundIds = array_map(
            fn (UnitRefundInterface $unitRefund) => $unitRefund->id(),
            $this->unitRefundFilter->filterUnitRefunds($unitRefunds, OrderItemUnitRefund::class),
        );

        $countOrderItemUnitRefundsBelongingToOrder = $this->countRefundsBelongingToOrderQuery
            ->count($orderItemUnitRefundIds, $orderNumber)
        ;

        if (count($orderItemUnitRefundIds) !== $countOrderItemUnitRefundsBelongingToOrder) {
            throw RefundUnitsNotBelongToOrder::withValidationConstraint(
                RefundUnitsValidationConstraintMessages::REFUND_UNITS_MUST_BELONG_TO_ORDER,
            );
        }
    }
}

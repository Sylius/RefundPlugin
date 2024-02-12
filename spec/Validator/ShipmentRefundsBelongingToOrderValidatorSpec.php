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

namespace spec\Sylius\RefundPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Doctrine\ORM\CountRefundsBelongingToOrderQueryInterface;
use Sylius\RefundPlugin\Exception\RefundUnitsNotBelongToOrder;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Validator\UnitRefundsBelongingToOrderValidatorInterface;

final class OrderItemUnitRefundsBelongingToOrderValidatorSpec extends ObjectBehavior
{
    function let(
        UnitRefundFilterInterface $unitRefundFilter,
        CountRefundsBelongingToOrderQueryInterface $countRefundsBelongingToOrderQuery,
    ): void {
        $this->beConstructedWith($unitRefundFilter, $countRefundsBelongingToOrderQuery);
    }

    function it_implements_unit_refunds_belonging_to_order_validator_interface(): void
    {
        $this->shouldHaveType(UnitRefundsBelongingToOrderValidatorInterface::class);
    }

    function it_throws_an_exception_if_some_order_item_unit_refunds_do_not_belong_to_the_order(
        UnitRefundFilterInterface $unitRefundFilter,
        CountRefundsBelongingToOrderQueryInterface $countRefundsBelongingToOrderQuery,
    ): void {
        $unitRefunds = [
            new OrderItemUnitRefund(1, 3000),
            $firstShipmentRefund = new ShipmentRefund(2, 5000),
            $secondShipmentRefund = new ShipmentRefund(3, 8000),
            new OrderItemUnitRefund(4, 13000),
        ];

        $unitRefundFilter
            ->filterUnitRefunds($unitRefunds, ShipmentRefund::class)
            ->willReturn([
                $firstShipmentRefund,
                $secondShipmentRefund,
            ])
        ;

        $countRefundsBelongingToOrderQuery->count([2, 3], '000001')->willReturn(1);

        $this
            ->shouldThrow(RefundUnitsNotBelongToOrder::class)
            ->during('validateUnits', [$unitRefunds, '000001'])
        ;
    }

    function it_does_not_throw_an_exception_if_all_order_item_unit_refunds_belong_to_the_order(
        UnitRefundFilterInterface $unitRefundFilter,
        CountRefundsBelongingToOrderQueryInterface $countRefundsBelongingToOrderQuery,
    ): void {
        $unitRefunds = [
            new OrderItemUnitRefund(1, 3000),
            $firstShipmentRefund = new ShipmentRefund(2, 5000),
            $secondShipmentRefund = new ShipmentRefund(3, 8000),
            new OrderItemUnitRefund(4, 13000),
        ];

        $unitRefundFilter
            ->filterUnitRefunds($unitRefunds, ShipmentRefund::class)
            ->willReturn([
                $firstShipmentRefund,
                $secondShipmentRefund,
            ])
        ;

        $countRefundsBelongingToOrderQuery->count([2, 3], '000001')->willReturn(2);

        $this
            ->shouldNotThrow()
            ->during('validateUnits', [$unitRefunds, '000001'])
        ;
    }
}

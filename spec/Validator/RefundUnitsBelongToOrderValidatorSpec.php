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

namespace spec\Sylius\RefundPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Doctrine\ORM\UnitRefundBelongsToOrderQueryInterface;
use Sylius\RefundPlugin\Exception\RefundUnitsNotBelongToOrder;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Validator\RefundUnitsBelongToOrderValidatorInterface;

final class RefundUnitsBelongToOrderValidatorSpec extends ObjectBehavior
{
    function let(
        UnitRefundBelongsToOrderQueryInterface $unitRefundBelongsToOrderQuery,
        UnitRefundFilterInterface $unitRefundFilter,
    ): void {
        $this->beConstructedWith($unitRefundBelongsToOrderQuery, $unitRefundFilter);
    }

    function it_implements_refund_units_belong_to_order_validator_interface(): void
    {
        $this->shouldImplement(RefundUnitsBelongToOrderValidatorInterface::class);
    }

    function it_throws_an_exception_if_all_types_of_unit_refund_do_not_belong_to_an_order(
        UnitRefundBelongsToOrderQueryInterface $unitRefundBelongsToOrderQuery,
        UnitRefundFilterInterface $unitRefundFilter,
    ): void {
        $unitRefundBelongsToOrderQuery->orderItemUnitRefundsBelongToOrder([1], '000001')->willReturn(false);
        $unitRefundBelongsToOrderQuery->shipmentRefundsBelongToOrder([2], '000001')->willReturn(false);

        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new ShipmentRefund(2, 500);

        $unitRefundFilter
            ->filterUnitRefunds(
                [$firstUnitRefund, $secondUnitRefund],
                OrderItemUnitRefund::class,
            )
            ->willReturn([$firstUnitRefund])
        ;

        $unitRefundFilter
            ->filterUnitRefunds(
                [$firstUnitRefund, $secondUnitRefund],
                ShipmentRefund::class,
            )
            ->willReturn([$secondUnitRefund])
        ;

        $this
            ->shouldThrow(RefundUnitsNotBelongToOrder::class)
            ->during('validateUnits', [
                [$firstUnitRefund, $secondUnitRefund],
                1,
            ])
        ;
    }

    function it_throws_an_exception_if_one_type_of_unit_refund_does_not_belong_to_an_order(
        UnitRefundBelongsToOrderQueryInterface $unitRefundBelongsToOrderQuery,
        UnitRefundFilterInterface $unitRefundFilter,
    ): void {
        $unitRefundBelongsToOrderQuery->orderItemUnitRefundsBelongToOrder([1], '000001')->willReturn(false);
        $unitRefundBelongsToOrderQuery->shipmentRefundsBelongToOrder([2], '000001')->willReturn(true);

        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new ShipmentRefund(2, 500);

        $unitRefundFilter
            ->filterUnitRefunds(
                [$firstUnitRefund, $secondUnitRefund],
                OrderItemUnitRefund::class,
            )
            ->willReturn([$firstUnitRefund])
        ;

        $unitRefundFilter
            ->filterUnitRefunds(
                [$firstUnitRefund, $secondUnitRefund],
                ShipmentRefund::class,
            )
            ->willReturn([$secondUnitRefund])
        ;

        $this
            ->shouldThrow(RefundUnitsNotBelongToOrder::class)
            ->during('validateUnits', [
                [$firstUnitRefund, $secondUnitRefund],
                1,
            ])
        ;
    }

    function it_does_not_throw_an_exception_if_unit_refunds_are_belonging_to_order(
        UnitRefundBelongsToOrderQueryInterface $unitRefundBelongsToOrderQuery,
        UnitRefundFilterInterface $unitRefundFilter,
    ): void {
        $unitRefundBelongsToOrderQuery->orderItemUnitRefundsBelongToOrder([1], '000001')->willReturn(true);
        $unitRefundBelongsToOrderQuery->shipmentRefundsBelongToOrder([2], '000001')->willReturn(true);

        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new ShipmentRefund(2, 500);

        $unitRefundFilter
            ->filterUnitRefunds(
                [$firstUnitRefund, $secondUnitRefund],
                OrderItemUnitRefund::class,
            )
            ->willReturn([$firstUnitRefund])
        ;

        $unitRefundFilter
            ->filterUnitRefunds(
                [$firstUnitRefund, $secondUnitRefund],
                ShipmentRefund::class,
            )
            ->willReturn([$secondUnitRefund])
        ;

        $this
            ->shouldNotThrow()
            ->during('validateUnits', [
                [$firstUnitRefund, $secondUnitRefund],
                1,
            ])
        ;
    }
}

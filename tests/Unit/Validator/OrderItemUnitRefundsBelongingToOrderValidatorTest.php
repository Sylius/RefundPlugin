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

namespace Tests\Sylius\RefundPlugin\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Doctrine\ORM\CountRefundsBelongingToOrderQueryInterface;
use Sylius\RefundPlugin\Exception\RefundUnitsNotBelongToOrder;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Validator\OrderItemUnitRefundsBelongingToOrderValidator;
use Sylius\RefundPlugin\Validator\UnitRefundsBelongingToOrderValidatorInterface;

final class OrderItemUnitRefundsBelongingToOrderValidatorTest extends TestCase
{
    private UnitRefundFilterInterface $unitRefundFilter;
    private CountRefundsBelongingToOrderQueryInterface $countRefundsBelongingToOrderQuery;
    private OrderItemUnitRefundsBelongingToOrderValidator $validator;

    protected function setUp(): void
    {
        $this->unitRefundFilter = $this->createMock(UnitRefundFilterInterface::class);
        $this->countRefundsBelongingToOrderQuery = $this->createMock(CountRefundsBelongingToOrderQueryInterface::class);
        $this->validator = new OrderItemUnitRefundsBelongingToOrderValidator(
            $this->unitRefundFilter,
            $this->countRefundsBelongingToOrderQuery
        );
    }

    /** @test */
    function it_implements_unit_refunds_belonging_to_order_validator_interface(): void
    {
        $this->assertInstanceOf(UnitRefundsBelongingToOrderValidatorInterface::class, $this->validator);
    }

    /** @test */
    function it_throws_an_exception_if_some_order_item_unit_refunds_do_not_belong_to_the_order(): void
    {
        $unitRefunds = [
            $firstOrderItemUnitRefund = new OrderItemUnitRefund(1, 3000),
            new ShipmentRefund(2, 5000),
            new ShipmentRefund(3, 8000),
            $secondOrderItemUnitRefund = new OrderItemUnitRefund(4, 13000),
        ];

        $this->unitRefundFilter
            ->expects($this->once())
            ->method('filterUnitRefunds')
            ->with($unitRefunds, OrderItemUnitRefund::class)
            ->willReturn([
                $firstOrderItemUnitRefund,
                $secondOrderItemUnitRefund,
            ]);

        $this->countRefundsBelongingToOrderQuery
            ->expects($this->once())
            ->method('count')
            ->with([1, 4], '000001')
            ->willReturn(1);

        $this->expectException(RefundUnitsNotBelongToOrder::class);

        $this->validator->validateUnits($unitRefunds, '000001');
    }

    /** @test */
    function it_does_not_throw_an_exception_if_all_order_item_unit_refunds_belong_to_the_order(): void
    {
        $unitRefunds = [
            $firstOrderItemUnitRefund = new OrderItemUnitRefund(1, 3000),
            new ShipmentRefund(2, 5000),
            new ShipmentRefund(3, 8000),
            $secondOrderItemUnitRefund = new OrderItemUnitRefund(4, 13000),
        ];

        $this->unitRefundFilter
            ->expects($this->once())
            ->method('filterUnitRefunds')
            ->with($unitRefunds, OrderItemUnitRefund::class)
            ->willReturn([
                $firstOrderItemUnitRefund,
                $secondOrderItemUnitRefund,
            ]);

        $this->countRefundsBelongingToOrderQuery
            ->expects($this->once())
            ->method('count')
            ->with([1, 4], '000001')
            ->willReturn(2);

        $this->validator->validateUnits($unitRefunds, '000001');

        // No exception should be thrown
        $this->addToAssertionCount(1);
    }
}
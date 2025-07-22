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

namespace Tests\Sylius\RefundPlugin\Unit\Filter;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Filter\UnitRefundFilter;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class UnitRefundFilterTest extends TestCase
{
    private UnitRefundFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new UnitRefundFilter();
    }

    /** @test */
    function it_implements_unit_refund_filter_interface(): void
    {
        self::assertInstanceOf(UnitRefundFilterInterface::class, $this->filter);
    }

    /** @test */
    function it_filters_unit_refunds_by_given_unit_refund_class(): void
    {
        $fourthUnitRefund = $this->createMock(UnitRefundInterface::class);
        $firstUnitRefund = new OrderItemUnitRefund(1, 1);
        $secondUnitRefund = new ShipmentRefund(2, 3);
        $thirdUnitRefund = new OrderItemUnitRefund(5, 8);

        $result = $this->filter->filterUnitRefunds(
            [
                $firstUnitRefund,
                $secondUnitRefund,
                'index_should_be_ignored' => $thirdUnitRefund,
                $fourthUnitRefund,
            ],
            OrderItemUnitRefund::class,
        );

        self::assertEquals([$firstUnitRefund, $thirdUnitRefund], $result);
    }

    /** @test */
    function it_throws_an_exception_if_at_least_one_of_given_units_does_not_implement_unit_refund_interface(): void
    {
        $fifthUnitRefund = $this->createMock(UnitRefundInterface::class);
        $firstUnitRefund = new OrderItemUnitRefund(1, 1);
        $secondUnitRefund = new ShipmentRefund(2, 3);
        $thirdUnitRefund = new OrderItemUnitRefund(5, 8);
        $fourthUnitRefund = new \stdClass();

        $this->expectException(\TypeError::class);

        $this->filter->filterUnitRefunds(
            [
                $firstUnitRefund,
                $secondUnitRefund,
                'index_should_be_ignored' => $thirdUnitRefund,
                $fourthUnitRefund,
                $fifthUnitRefund,
            ],
            OrderItemUnitRefund::class,
        );
    }
}

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

namespace spec\Sylius\RefundPlugin\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class UnitRefundFilterSpec extends ObjectBehavior
{
    function it_implements_unit_refund_filter_interface(): void
    {
        $this->shouldImplement(UnitRefundFilterInterface::class);
    }

    function it_filters_unit_refunds_by_given_unit_refund_class(UnitRefundInterface $fourthUnitRefund): void
    {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1);
        $secondUnitRefund = new ShipmentRefund(2, 3);
        $thirdUnitRefund = new OrderItemUnitRefund(5, 8);

        $this
            ->filterUnitRefunds(
                [
                    $firstUnitRefund,
                    $secondUnitRefund,
                    'index_should_be_ignored' => $thirdUnitRefund,
                    $fourthUnitRefund,
                ],
                OrderItemUnitRefund::class,
            )
            ->shouldReturn([$firstUnitRefund, $thirdUnitRefund])
        ;
    }

    function it_throws_an_exception_if_at_least_one_of_given_units_does_not_implement_unit_refund_interface(
        UnitRefundInterface $fifthUnitRefund,
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1);
        $secondUnitRefund = new ShipmentRefund(2, 3);
        $thirdUnitRefund = new OrderItemUnitRefund(5, 8);
        $fourthUnitRefund = new \stdClass();

        $this
            ->shouldThrow()
            ->during(
                'filterUnitRefunds',
                [
                    [
                        $firstUnitRefund,
                        $secondUnitRefund,
                        'index_should_be_ignored' => $thirdUnitRefund,
                        $fourthUnitRefund,
                        $fifthUnitRefund,
                    ],
                    OrderItemUnitRefund::class,
                ],
            )
        ;
    }
}

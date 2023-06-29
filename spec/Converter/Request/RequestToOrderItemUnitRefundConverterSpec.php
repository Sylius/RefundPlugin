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

namespace spec\Sylius\RefundPlugin\Converter\Request;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RequestToOrderItemUnitRefundConverterSpec extends ObjectBehavior
{
    function let(RefundUnitsConverterInterface $refundUnitsConverter): void
    {
        $this->beConstructedWith($refundUnitsConverter);
    }

    function it_is_request_to_refund_units_converter(): void
    {
        $this->shouldImplement(RequestToRefundUnitsConverterInterface::class);
    }

    function it_creates_order_item_units_refunds_from_request(
        RefundUnitsConverterInterface $refundUnitsConverter,
        Request $request,
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new OrderItemUnitRefund(2, 3000);

        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                1 => ['full' => 'on'],
                2 => ['full' => 'on'],
            ],
        ]);

        $refundUnitsConverter
            ->convert(
                [
                    1 => ['full' => 'on'],
                    2 => ['full' => 'on'],
                ],
                OrderItemUnitRefund::class,
            )
            ->willReturn([$firstUnitRefund, $secondUnitRefund])
        ;

        $this->convert($request)->shouldReturn([$firstUnitRefund, $secondUnitRefund]);
    }
}

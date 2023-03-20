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
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\Request;

final class RequestToRefundUnitsConverterSpec extends ObjectBehavior
{
    function let(RequestToRefundUnitsConverterInterface $orderItemUnitConverter, RequestToRefundUnitsConverterInterface $shipmentConverter): void
    {
        $this->beConstructedWith([$orderItemUnitConverter, $shipmentConverter]);
    }

    function it_is_request_to_refund_units_converter(): void
    {
        $this->shouldImplement(RequestToRefundUnitsConverterInterface::class);
    }

    function it_creates_final_refund_list_from_aggreggated_servies(
        RequestToRefundUnitsConverterInterface $orderItemUnitConverter,
        RequestToRefundUnitsConverterInterface $shipmentConverter,
        Request $request,
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new OrderItemUnitRefund(2, 3000);
        $shipmentRefund = new ShipmentRefund(1, 5000);

        $orderItemUnitConverter->convert($request)->willReturn([$firstUnitRefund, $secondUnitRefund]);
        $shipmentConverter->convert($request)->willReturn([$shipmentRefund]);

        $this->convert($request)->shouldReturn([$firstUnitRefund, $secondUnitRefund, $shipmentRefund]);
    }
}

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

namespace spec\Sylius\RefundPlugin\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Converter\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RequestToRefundUnitsConverterSpec extends ObjectBehavior
{
    function let(RefundUnitsConverterInterface $refundUnitsConverter): void
    {
        $this->beConstructedWith($refundUnitsConverter);
    }

    function it_implements_refund_units_command_creator_interface(): void
    {
        $this->shouldImplement(RequestToRefundUnitsConverterInterface::class);
    }

    function it_creates_refund_units_command_from_request(
        RefundUnitsConverterInterface $refundUnitsConverter,
        Request $request,
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new OrderItemUnitRefund(2, 3000);
        $shipmentRefund = new ShipmentRefund(1, 5000);

        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                1 => ['full' => 'on'],
                2 => ['full' => 'on'],
            ],
            'sylius_refund_shipments' => [
                1 => ['full' => 'on'],
            ],
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $refundUnitsConverter
            ->convert(
                [
                    1 => ['full' => 'on'],
                    2 => ['full' => 'on'],
                ],
                RefundType::orderItemUnit(),
                OrderItemUnitRefund::class,
            )
            ->willReturn([$firstUnitRefund, $secondUnitRefund])
        ;
        $refundUnitsConverter
            ->convert(
                [1 => ['full' => 'on']],
                RefundType::shipment(),
                ShipmentRefund::class,
            )
            ->willReturn([$shipmentRefund])
        ;

        $this->convert($request)->shouldReturn([$firstUnitRefund, $secondUnitRefund, $shipmentRefund]);
    }
}

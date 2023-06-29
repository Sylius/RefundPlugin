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
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RequestToShipmentRefundConverterSpec extends ObjectBehavior
{
    function let(RefundUnitsConverterInterface $refundUnitsConverter): void
    {
        $this->beConstructedWith($refundUnitsConverter);
    }

    function it_is_request_to_refund_units_converter(): void
    {
        $this->shouldImplement(RequestToRefundUnitsConverterInterface::class);
    }

    function it_creates_shipment_refund_units_from_request(
        RefundUnitsConverterInterface $refundUnitsConverter,
        Request $request,
    ): void {
        $shipmentRefund = new ShipmentRefund(1, 5000);

        $request->request = new ParameterBag([
            'sylius_refund_shipments' => [
                1 => ['full' => 'on'],
            ],
        ]);

        $refundUnitsConverter
            ->convert(
                [1 => ['full' => 'on']],
                ShipmentRefund::class,
            )
            ->willReturn([$shipmentRefund])
        ;

        $this->convert($request)->shouldReturn([$shipmentRefund]);
    }
}

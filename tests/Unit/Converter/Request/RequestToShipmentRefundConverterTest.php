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

namespace Tests\Sylius\RefundPlugin\Unit\Converter\Request;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToShipmentRefundConverter;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

final class RequestToShipmentRefundConverterTest extends TestCase
{
    private RefundUnitsConverterInterface $refundUnitsConverter;
    private RequestToShipmentRefundConverter $converter;

    protected function setUp(): void
    {
        $this->refundUnitsConverter = $this->getMockBuilder(RefundUnitsConverterInterface::class)
            ->addMethods(['convert'])
            ->getMock();
        $this->converter = new RequestToShipmentRefundConverter($this->refundUnitsConverter);
    }

    /** @test */
    function it_is_request_to_refund_units_converter(): void
    {
        $this->assertInstanceOf(RequestToRefundUnitsConverterInterface::class, $this->converter);
    }

    /** @test */
    function it_creates_shipment_refund_units_from_request(): void
    {
        $request = $this->createMock(Request::class);
        $shipmentRefund = new ShipmentRefund(1, 5000);

        $request->request = new InputBag([
            'sylius_refund_shipments' => [
                1 => ['full' => 'on'],
            ],
        ]);

        $this->refundUnitsConverter
            ->expects($this->once())
            ->method('convert')
            ->with(
                [1 => ['full' => 'on']],
                ShipmentRefund::class,
            )
            ->willReturn([$shipmentRefund]);

        $result = $this->converter->convert($request);

        $this->assertEquals([$shipmentRefund], $result);
    }
}
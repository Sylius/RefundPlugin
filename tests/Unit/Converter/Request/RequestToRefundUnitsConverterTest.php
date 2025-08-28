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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverter;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\Request;

final class RequestToRefundUnitsConverterTest extends TestCase
{
    private RequestToRefundUnitsConverterInterface&MockObject $orderItemUnitConverter;

    private RequestToRefundUnitsConverterInterface&MockObject $shipmentConverter;

    private RequestToRefundUnitsConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemUnitConverter = $this->createMock(RequestToRefundUnitsConverterInterface::class);
        $this->shipmentConverter = $this->createMock(RequestToRefundUnitsConverterInterface::class);
        $this->converter = new RequestToRefundUnitsConverter([$this->orderItemUnitConverter, $this->shipmentConverter]);
    }

    #[Test]
    public function it_is_request_to_refund_units_converter(): void
    {
        self::assertInstanceOf(RequestToRefundUnitsConverterInterface::class, $this->converter);
    }

    #[Test]
    public function it_creates_final_refund_list_from_aggregated_services(): void
    {
        $request = $this->createMock(Request::class);
        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new OrderItemUnitRefund(2, 3000);
        $shipmentRefund = new ShipmentRefund(1, 5000);

        $this->orderItemUnitConverter
            ->expects(self::once())
            ->method('convert')
            ->with($request)
            ->willReturn([$firstUnitRefund, $secondUnitRefund]);

        $this->shipmentConverter
            ->expects(self::once())
            ->method('convert')
            ->with($request)
            ->willReturn([$shipmentRefund]);

        $result = $this->converter->convert($request);

        self::assertEquals([$firstUnitRefund, $secondUnitRefund, $shipmentRefund], $result);
    }
}

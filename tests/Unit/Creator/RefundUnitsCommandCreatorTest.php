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

namespace Sylius\RefundPlugin\Tests\Unit\Creator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Creator\RefundUnitsCommandCreator;
use Sylius\RefundPlugin\Creator\RequestCommandCreatorInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsCommandCreatorTest extends TestCase
{
    private RequestToRefundUnitsConverterInterface|MockObject $refundUnitsConverter;
    private RefundUnitsCommandCreator $refundUnitsCommandCreator;

    protected function setUp(): void
    {
        $this->refundUnitsConverter = $this->createMock(RequestToRefundUnitsConverterInterface::class);
        $this->refundUnitsCommandCreator = new RefundUnitsCommandCreator($this->refundUnitsConverter);
    }

    public function testItImplementsRefundUnitsCommandCreatorInterface(): void
    {
        $this->assertInstanceOf(RequestCommandCreatorInterface::class, $this->refundUnitsCommandCreator);
    }

    public function testItCreatesRefundUnitsCommandFromRequest(): void
    {
        $firstUnitRefund = $this->createMock(UnitRefundInterface::class);
        $secondUnitRefund = $this->createMock(UnitRefundInterface::class);
        $shipmentRefund = $this->createMock(UnitRefundInterface::class);

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new InputBag([
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $this->refundUnitsConverter->expects($this->once())
            ->method('convert')
            ->with($request)
            ->willReturn([$firstUnitRefund, $secondUnitRefund, $shipmentRefund]);

        $result = $this->refundUnitsCommandCreator->fromRequest($request);

        $this->assertInstanceOf(RefundUnits::class, $result);
        $this->assertSame('00001111', $result->orderNumber());
        $this->assertSame([$firstUnitRefund, $secondUnitRefund, $shipmentRefund], $result->units());
        $this->assertSame(1, $result->paymentMethodId());
        $this->assertSame('Comment', $result->comment());
    }

    public function testItThrowsExceptionIfThereIsNoUnitsNorShipmentsProvided(): void
    {
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new InputBag(['sylius_refund_payment_method' => 1]);

        $this->refundUnitsConverter->expects($this->once())
            ->method('convert')
            ->with($request)
            ->willReturn([]);

        $this->expectException(\InvalidArgumentException::class);

        $this->refundUnitsCommandCreator->fromRequest($request);
    }

    public function testItThrowsExceptionIfThereIsNoOrderNumberProvided(): void
    {
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag([]);

        $this->expectException(\InvalidArgumentException::class);

        $this->refundUnitsCommandCreator->fromRequest($request);
    }
}
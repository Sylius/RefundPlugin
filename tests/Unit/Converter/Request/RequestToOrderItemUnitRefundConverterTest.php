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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToOrderItemUnitRefundConverter;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

final class RequestToOrderItemUnitRefundConverterTest extends TestCase
{
    private RefundUnitsConverterInterface&MockObject $refundUnitsConverter;

    private RequestToOrderItemUnitRefundConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refundUnitsConverter = $this->getMockBuilder(RefundUnitsConverterInterface::class)
            ->addMethods(['convert'])
            ->getMock();
        $this->converter = new RequestToOrderItemUnitRefundConverter($this->refundUnitsConverter);
    }

    /** @test */
    public function it_is_request_to_refund_units_converter(): void
    {
        self::assertInstanceOf(RequestToRefundUnitsConverterInterface::class, $this->converter);
    }

    /** @test */
    public function it_creates_order_item_units_refunds_from_request(): void
    {
        $request = $this->createMock(Request::class);
        $firstUnitRefund = new OrderItemUnitRefund(1, 1000);
        $secondUnitRefund = new OrderItemUnitRefund(2, 3000);

        $request->request = new InputBag([
            'sylius_refund_units' => [
                1 => ['full' => 'on'],
                2 => ['full' => 'on'],
            ],
        ]);

        $this->refundUnitsConverter
            ->expects(self::once())
            ->method('convert')
            ->with(
                [
                    1 => ['full' => 'on'],
                    2 => ['full' => 'on'],
                ],
                OrderItemUnitRefund::class,
            )
            ->willReturn([$firstUnitRefund, $secondUnitRefund]);

        $result = $this->converter->convert($request);

        self::assertEquals([$firstUnitRefund, $secondUnitRefund], $result);
    }
}

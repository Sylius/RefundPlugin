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

namespace Tests\Sylius\RefundPlugin\Unit\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Provider\RefundedShipmentFeeProvider;
use Sylius\RefundPlugin\Provider\RefundedShipmentFeeProviderInterface;

final class RefundedShipmentFeeProviderTest extends TestCase
{
    private RepositoryInterface&MockObject $adjustmentRepository;
    private RefundedShipmentFeeProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adjustmentRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new RefundedShipmentFeeProvider($this->adjustmentRepository);
    }

    /** @test */
    function it_is_initializable(): void
    {
        self::assertInstanceOf(RefundedShipmentFeeProvider::class, $this->provider);
    }

    /** @test */
    function it_implements_refunded_shipment_fee_provider_interface(): void
    {
        self::assertInstanceOf(RefundedShipmentFeeProviderInterface::class, $this->provider);
    }

    /** @test */
    function it_returns_fee_from_shipping_adjustment(): void
    {
        $shippingAdjustment = $this->createMock(AdjustmentInterface::class);

        $this->adjustmentRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($shippingAdjustment);

        $shippingAdjustment
            ->expects(self::once())
            ->method('getType')
            ->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        $shippingAdjustment
            ->expects(self::once())
            ->method('getAmount')
            ->willReturn(1000);

        $result = $this->provider->getFeeOfShipment(1);

        self::assertSame(1000, $result);
    }

    /** @test */
    function it_throws_exception_if_there_is_no_adjustment_with_given_id(): void
    {
        $this->adjustmentRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->provider->getFeeOfShipment(1);
    }

    /** @test */
    function it_throws_exception_if_adjustment_is_not_shipping_adjustment(): void
    {
        $adjustment = $this->createMock(AdjustmentInterface::class);

        $this->adjustmentRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($adjustment);

        $adjustment
            ->expects(self::once())
            ->method('getType')
            ->willReturn('some_other_type');

        $this->expectException(\InvalidArgumentException::class);

        $this->provider->getFeeOfShipment(1);
    }
}

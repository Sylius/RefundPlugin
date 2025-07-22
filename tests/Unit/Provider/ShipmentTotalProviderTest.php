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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;
use Sylius\RefundPlugin\Provider\ShipmentTotalProvider;

final class ShipmentTotalProviderTest extends TestCase
{
    private RepositoryInterface&MockObject $adjustmentRepository;

    private ShipmentTotalProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adjustmentRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new ShipmentTotalProvider($this->adjustmentRepository);
    }

    /** @test */
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(ShipmentTotalProvider::class, $this->provider);
    }

    /** @test */
    public function it_is_refund_unit_total_provider(): void
    {
        self::assertInstanceOf(RefundUnitTotalProviderInterface::class, $this->provider);
    }

    /** @test */
    public function it_returns_shipment_total_to_refund(): void
    {
        $shippingAdjustment = $this->createMock(AdjustmentInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);

        $this->adjustmentRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment);

        $shippingAdjustment
            ->expects(self::once())
            ->method('getShipment')
            ->willReturn($shipment);

        $shipment
            ->expects(self::once())
            ->method('getAdjustmentsTotal')
            ->willReturn(1000);

        $result = $this->provider->getRefundUnitTotal(1);

        self::assertSame(1000, $result);
    }

    /** @test */
    public function it_throws_exception_if_there_is_no_shipment_with_given_id(): void
    {
        $this->adjustmentRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->provider->getRefundUnitTotal(1);
    }
}

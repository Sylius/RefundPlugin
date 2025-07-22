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

namespace Tests\Sylius\RefundPlugin\Unit\Refunder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Refunder\OrderShipmentsRefunder;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderShipmentsRefunderTest extends TestCase
{
    private RefundCreatorInterface&MockObject $refundCreator;
    private MessageBusInterface&MockObject $eventBus;
    private UnitRefundFilterInterface&MockObject $unitRefundFilter;
    private OrderShipmentsRefunder $refunder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refundCreator = $this->createMock(RefundCreatorInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->unitRefundFilter = $this->createMock(UnitRefundFilterInterface::class);

        $this->refunder = new OrderShipmentsRefunder(
            $this->refundCreator,
            $this->eventBus,
            $this->unitRefundFilter,
        );
    }

    /** @test */
    public function it_implements_refunder_interface(): void
    {
        self::assertInstanceOf(RefunderInterface::class, $this->refunder);
    }

    /** @test */
    public function it_creates_refund_for_each_shipment_and_dispatch_proper_event(): void
    {
        $shipmentRefund = new ShipmentRefund(4, 2500);
        $refunds = [$shipmentRefund, new OrderItemUnitRefund(8, 1000)];

        $this->unitRefundFilter
            ->expects(self::once())
            ->method('filterUnitRefunds')
            ->with($refunds, ShipmentRefund::class)
            ->willReturn([$shipmentRefund]);

        $this->refundCreator
            ->expects(self::once())
            ->method('__invoke')
            ->with('000222', 4, 2500, RefundType::shipment());

        $event = new ShipmentRefunded('000222', 4, 2500);
        $this->eventBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $result = $this->refunder->refundFromOrder($refunds, '000222');

        self::assertEquals(2500, $result);
    }
}

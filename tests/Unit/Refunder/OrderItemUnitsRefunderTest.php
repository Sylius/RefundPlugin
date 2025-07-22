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
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Refunder\OrderItemUnitsRefunder;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemUnitsRefunderTest extends TestCase
{
    private RefundCreatorInterface|MockObject $refundCreator;
    private MessageBusInterface|MockObject $eventBus;
    private UnitRefundFilterInterface|MockObject $unitRefundFilter;
    private OrderItemUnitsRefunder $refunder;

    protected function setUp(): void
    {
        $this->refundCreator = $this->createMock(RefundCreatorInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->unitRefundFilter = $this->createMock(UnitRefundFilterInterface::class);

        $this->refunder = new OrderItemUnitsRefunder(
            $this->refundCreator,
            $this->eventBus,
            $this->unitRefundFilter,
        );
    }

    /** @test */
    public function it_implements_refunder_interface(): void
    {
        $this->assertInstanceOf(RefunderInterface::class, $this->refunder);
    }

    /** @test */
    public function it_creates_refund_for_each_unit_and_dispatch_proper_event(): void
    {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 1000);
        $shipmentRefund = new ShipmentRefund(3, 1000);

        $this->unitRefundFilter
            ->expects($this->once())
            ->method('filterUnitRefunds')
            ->with([$firstUnitRefund, $secondUnitRefund, $shipmentRefund], OrderItemUnitRefund::class)
            ->willReturn([$firstUnitRefund, $secondUnitRefund]);

        $this->refundCreator
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->withConsecutive(
                ['000222', 1, 1500, RefundType::orderItemUnit()],
                ['000222', 3, 1000, RefundType::orderItemUnit()]
            );

        $firstEvent = new UnitRefunded('000222', 1, 1500);
        $secondEvent = new UnitRefunded('000222', 3, 1000);

        $this->eventBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$firstEvent],
                [$secondEvent]
            )
            ->willReturnOnConsecutiveCalls(
                new Envelope($firstEvent),
                new Envelope($secondEvent)
            );

        $result = $this->refunder->refundFromOrder([$firstUnitRefund, $secondUnitRefund, $shipmentRefund], '000222');

        $this->assertEquals(2500, $result);
    }
}
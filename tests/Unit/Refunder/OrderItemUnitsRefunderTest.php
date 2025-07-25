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

use PHPUnit\Framework\Attributes\Test;
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
    private RefundCreatorInterface&MockObject $refundCreator;

    private MessageBusInterface&MockObject $eventBus;

    private UnitRefundFilterInterface&MockObject $unitRefundFilter;

    private OrderItemUnitsRefunder $refunder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refundCreator = $this->createMock(RefundCreatorInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->unitRefundFilter = $this->createMock(UnitRefundFilterInterface::class);

        $this->refunder = new OrderItemUnitsRefunder(
            $this->refundCreator,
            $this->eventBus,
            $this->unitRefundFilter,
        );
    }

    #[Test]
    public function it_implements_refunder_interface(): void
    {
        self::assertInstanceOf(RefunderInterface::class, $this->refunder);
    }

    #[Test]
    public function it_creates_refund_for_each_unit_and_dispatch_proper_event(): void
    {
        $firstUnitRefund = new OrderItemUnitRefund(1, 1500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 1000);
        $shipmentRefund = new ShipmentRefund(3, 1000);

        $this->unitRefundFilter
            ->expects(self::once())
            ->method('filterUnitRefunds')
            ->with([$firstUnitRefund, $secondUnitRefund, $shipmentRefund], OrderItemUnitRefund::class)
            ->willReturn([$firstUnitRefund, $secondUnitRefund]);

        $this->refundCreator
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->willReturnCallback(function ($orderNumber, $unitId, $amount, $refundType) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    $this->assertEquals('000222', $orderNumber);
                    $this->assertEquals(1, $unitId);
                    $this->assertEquals(1500, $amount);
                    $this->assertEquals(RefundType::orderItemUnit(), $refundType);
                } elseif ($callCount === 2) {
                    $this->assertEquals('000222', $orderNumber);
                    $this->assertEquals(3, $unitId);
                    $this->assertEquals(1000, $amount);
                    $this->assertEquals(RefundType::orderItemUnit(), $refundType);
                }
            });

        $firstEvent = new UnitRefunded('000222', 1, 1500);
        $secondEvent = new UnitRefunded('000222', 3, 1000);

        $this->eventBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function ($event) use ($firstEvent, $secondEvent) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    $this->assertEquals($firstEvent, $event);

                    return new Envelope($firstEvent);
                }
                if ($callCount === 2) {
                    $this->assertEquals($secondEvent, $event);

                    return new Envelope($secondEvent);
                }

                return new Envelope($event);
            });

        $result = $this->refunder->refundFromOrder([$firstUnitRefund, $secondUnitRefund, $shipmentRefund], '000222');

        self::assertEquals(2500, $result);
    }
}

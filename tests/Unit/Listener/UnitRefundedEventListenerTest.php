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

namespace Tests\Sylius\RefundPlugin\Unit\Listener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\Event\UnitRefundedInterface;
use Sylius\RefundPlugin\Listener\UnitRefundedEventListener;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface;

final class UnitRefundedEventListenerTest extends TestCase
{
    private OrderPartiallyRefundedStateResolverInterface&MockObject $orderPartiallyRefundedStateResolver;

    private UnitRefundedEventListener $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderPartiallyRefundedStateResolver = $this->createMock(OrderPartiallyRefundedStateResolverInterface::class);
        $this->listener = new UnitRefundedEventListener($this->orderPartiallyRefundedStateResolver);
    }

    /** @test */
    public function it_resolves_order_partially_refunded_state_with_unit_refunded_event(): void
    {
        $this->orderPartiallyRefundedStateResolver
            ->expects(self::once())
            ->method('resolve')
            ->with('000777');

        $this->listener->__invoke(new UnitRefunded('000777', 10, 1000));
    }

    /** @test */
    public function it_resolves_order_partially_refunded_state_with_shipment_refunded_event(): void
    {
        $this->orderPartiallyRefundedStateResolver
            ->expects(self::once())
            ->method('resolve')
            ->with('000777');

        $this->listener->__invoke(new ShipmentRefunded('000777', 10, 1000));
    }

    /** @test */
    public function it_resolves_order_partially_refunded_state_with_an_event_implementing_unit_refunded_interface(): void
    {
        $unitRefunded = $this->createMock(UnitRefundedInterface::class);

        $unitRefunded
            ->expects(self::once())
            ->method('orderNumber')
            ->willReturn('000777');

        $this->orderPartiallyRefundedStateResolver
            ->expects(self::once())
            ->method('resolve')
            ->with('000777');

        $this->listener->__invoke($unitRefunded);
    }
}

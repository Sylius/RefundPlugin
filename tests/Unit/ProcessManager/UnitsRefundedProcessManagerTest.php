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

namespace Tests\Sylius\RefundPlugin\Unit\ProcessManager;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManager;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManagerInterface;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface;

final class UnitsRefundedProcessManagerTest extends TestCase
{
    private UnitsRefundedProcessStepInterface&MockObject $creditMemoProcessManager;

    private UnitsRefundedProcessStepInterface&MockObject $refundPaymentProcessManager;

    private UnitsRefundedProcessManager $unitsRefundedProcessManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->creditMemoProcessManager = $this->createMock(UnitsRefundedProcessStepInterface::class);
        $this->refundPaymentProcessManager = $this->createMock(UnitsRefundedProcessStepInterface::class);

        $this->unitsRefundedProcessManager = new UnitsRefundedProcessManager([
            $this->creditMemoProcessManager,
            $this->refundPaymentProcessManager,
        ]);
    }

    /** @test */
    public function it_implements_units_refunded_process_manager_interface(): void
    {
        self::assertInstanceOf(UnitsRefundedProcessManagerInterface::class, $this->unitsRefundedProcessManager);
    }

    /** @test */
    public function it_triggers_all_process_steps_if_all_are_successful(): void
    {
        $unitRefunds = [
            new OrderItemUnitRefund(1, 1000),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(1, 500),
            new ShipmentRefund(2, 1000),
        ];
        $event = new UnitsRefunded('000222', $unitRefunds, 1, 1500, 'USD', 'Comment');

        $this->creditMemoProcessManager
            ->expects(self::once())
            ->method('next')
            ->with($event);

        $this->refundPaymentProcessManager
            ->expects(self::once())
            ->method('next')
            ->with($event);

        ($this->unitsRefundedProcessManager)($event);
    }
}

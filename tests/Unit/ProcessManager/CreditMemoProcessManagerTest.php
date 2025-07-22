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
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\ProcessManager\CreditMemoProcessManager;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreditMemoProcessManagerTest extends TestCase
{
    private MessageBusInterface&MockObject $commandBus;
    private CreditMemoProcessManager $creditMemoProcessManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->creditMemoProcessManager = new CreditMemoProcessManager($this->commandBus);
    }

    /** @test */
    function it_implements_units_refunded_process_step_interface(): void
    {
        self::assertInstanceOf(UnitsRefundedProcessStepInterface::class, $this->creditMemoProcessManager);
    }

    /** @test */
    function it_reacts_on_units_generated_event_and_dispatch_generate_credit_memo_command(): void
    {
        $unitRefunds = [
            new OrderItemUnitRefund(1, 1000),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(1, 500),
            new ShipmentRefund(2, 1000),
        ];

        $command = new GenerateCreditMemo('000222', 3000, $unitRefunds, 'Comment');

        $this->commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command));

        $this->creditMemoProcessManager->next(new UnitsRefunded('000222', $unitRefunds, 1, 3000, 'USD', 'Comment'));
    }
}

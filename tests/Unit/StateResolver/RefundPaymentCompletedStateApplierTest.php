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

namespace Tests\Sylius\RefundPlugin\Unit\StateResolver;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplier;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplierInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentTransitions;

final class RefundPaymentCompletedStateApplierTest extends TestCase
{
    private StateMachineInterface&MockObject $stateMachineFactory;

    private EntityManagerInterface&MockObject $refundPaymentManager;

    private RefundPaymentCompletedStateApplier $applier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachineFactory = $this->createMock(StateMachineInterface::class);
        $this->refundPaymentManager = $this->createMock(EntityManagerInterface::class);

        $this->applier = new RefundPaymentCompletedStateApplier($this->stateMachineFactory, $this->refundPaymentManager);
    }

    /** @test */
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(RefundPaymentCompletedStateApplier::class, $this->applier);
    }

    /** @test */
    public function it_implements_refund_payment_completed_state_applier_interface(): void
    {
        self::assertInstanceOf(RefundPaymentCompletedStateApplierInterface::class, $this->applier);
    }

    /** @test */
    public function it_applies_complete_transition_on_refund_payment(): void
    {
        $stateMachine = $this->createMock(StateMachineInterface::class);
        $refundPaymentManager = $this->createMock(EntityManagerInterface::class);
        $refundPayment = $this->createMock(RefundPaymentInterface::class);

        $applier = new RefundPaymentCompletedStateApplier($stateMachine, $refundPaymentManager);

        $stateMachine
            ->expects(self::once())
            ->method('apply')
            ->with($refundPayment, RefundPaymentTransitions::GRAPH, RefundPaymentTransitions::TRANSITION_COMPLETE);

        $refundPaymentManager
            ->expects(self::once())
            ->method('flush');

        $applier->apply($refundPayment);
    }

    /** @test */
    public function it_uses_winzou_state_machine_if_abstraction_not_passed_to_apply_complete_transition_on_refund_payment(): void
    {
        $refundPayment = $this->createMock(RefundPaymentInterface::class);

        $this->stateMachineFactory
            ->expects(self::once())
            ->method('apply')
            ->with($refundPayment, RefundPaymentTransitions::GRAPH, RefundPaymentTransitions::TRANSITION_COMPLETE);

        $this->refundPaymentManager
            ->expects(self::once())
            ->method('flush');

        $this->applier->apply($refundPayment);
    }
}

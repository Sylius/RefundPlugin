<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\StateResolver;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplier;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplierInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentTransitions;

final class RefundPaymentCompletedStateApplierSpec extends ObjectBehavior
{
    function let(StateMachineFactoryInterface $stateMachineFactory, ObjectManager $refundPaymentManager): void
    {
        $this->beConstructedWith($stateMachineFactory, $refundPaymentManager);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RefundPaymentCompletedStateApplier::class);
    }

    function it_implements_refund_payment_completed_state_applier_interface(): void
    {
        $this->shouldImplement(RefundPaymentCompletedStateApplierInterface::class);
    }

    function it_applies_complete_transition_on_refund_payment(
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        ObjectManager $refundPaymentManager,
        RefundPaymentInterface $refundPayment
    ): void {
        $stateMachineFactory->get($refundPayment, RefundPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(RefundPaymentTransitions::TRANSITION_COMPLETE)->shouldBeCalled();

        $refundPaymentManager->flush()->shouldBeCalled();

        $this->apply($refundPayment);
    }
}

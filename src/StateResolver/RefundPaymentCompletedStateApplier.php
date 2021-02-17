<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

use Doctrine\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentCompletedStateApplier implements RefundPaymentCompletedStateApplierInterface
{
    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var ObjectManager */
    private $refundPaymentManager;

    public function __construct(StateMachineFactoryInterface $stateMachineFactory, ObjectManager $refundPaymentManager)
    {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->refundPaymentManager = $refundPaymentManager;
    }

    public function apply(RefundPaymentInterface $refundPayment): void
    {
        $this->stateMachineFactory
            ->get($refundPayment, RefundPaymentTransitions::GRAPH)
            ->apply(RefundPaymentTransitions::TRANSITION_COMPLETE)
        ;

        $this->refundPaymentManager->flush();
    }
}

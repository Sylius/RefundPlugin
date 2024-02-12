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

namespace Sylius\RefundPlugin\StateResolver;

use Doctrine\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentCompletedStateApplier implements RefundPaymentCompletedStateApplierInterface
{
    private StateMachineFactoryInterface $stateMachineFactory;

    private ObjectManager $refundPaymentManager;

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

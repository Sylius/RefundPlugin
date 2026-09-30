<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentContext implements Context
{
    public function __construct(
        private readonly StateMachineInterface $stateMachineFactory,
        private readonly SharedStorageInterface $sharedStorage
    ) {
    }

    #[Given('the payment of order :order failed')]
    public function paymentOfOrderFailed(OrderInterface $order): void
    {
        $payment = $order->getLastPayment();
        $this->stateMachineFactory->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_FAIL);
    }

    #[Given('/^the customer chose ("[^"]+" payment) method$/')]
    public function theCustomerChosePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $lastPayment = $order->getLastPayment();
        $lastPayment->setMethod($paymentMethod);

        $this->sharedStorage->set('payment', $lastPayment);
    }

    #[Given('/^(this payment) has been paid$/')]
    public function andThisPaymentHasBeenPaid(PaymentInterface $payment): void
    {
        $this->stateMachineFactory->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE);
    }
}

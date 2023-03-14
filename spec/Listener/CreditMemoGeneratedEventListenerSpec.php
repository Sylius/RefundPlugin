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

namespace spec\Sylius\RefundPlugin\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Command\SendCreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreditMemoGeneratedEventListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    function it_sends_an_email_to_customer_for_whose_order_credit_memo_was_generated(
        MessageBusInterface $commandBus,
    ): void {
        $event = new CreditMemoGenerated('01/01/000002', '000222');

        $commandBus->dispatch(new SendCreditMemo('01/01/000002'))->willReturn(new Envelope($event))->shouldBeCalled();

        $this($event);
    }
}

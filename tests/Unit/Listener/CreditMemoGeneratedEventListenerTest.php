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

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Command\SendCreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Listener\CreditMemoGeneratedEventListener;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreditMemoGeneratedEventListenerTest extends TestCase
{
    private MessageBusInterface $commandBus;
    private CreditMemoGeneratedEventListener $listener;

    protected function setUp(): void
    {
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->listener = new CreditMemoGeneratedEventListener($this->commandBus);
    }

    /** @test */
    function it_sends_an_email_to_customer_for_whose_order_credit_memo_was_generated(): void
    {
        $event = new CreditMemoGenerated('01/01/000002', '000222');

        $this->commandBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(new SendCreditMemo('01/01/000002'))
            ->willReturn(new Envelope($event));

        $this->listener->__invoke($event);
    }
}
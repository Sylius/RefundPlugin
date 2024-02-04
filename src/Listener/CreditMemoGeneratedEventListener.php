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

namespace Sylius\RefundPlugin\Listener;

use Sylius\RefundPlugin\Command\SendCreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreditMemoGeneratedEventListener
{
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(CreditMemoGenerated $event): void
    {
        $this->commandBus->dispatch(new SendCreditMemo($event->number()));
    }
}

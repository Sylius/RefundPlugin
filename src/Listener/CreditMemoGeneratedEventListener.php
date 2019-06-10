<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Sylius\RefundPlugin\Command\SendCreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreditMemoGeneratedEventListener
{
    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(CreditMemoGenerated $event): void
    {
        $this->commandBus->dispatch(new SendCreditMemo($event->number()));
    }
}

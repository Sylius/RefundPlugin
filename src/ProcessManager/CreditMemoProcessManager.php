<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ProcessManager;

use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreditMemoProcessManager
{
    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(UnitsRefunded $event): void
    {
        $this->commandBus->dispatch(new GenerateCreditMemo(
            $event->orderNumber(),
            $event->amount(),
            $event->units(),
            $event->shipments(),
            $event->comment()
        ));
    }
}

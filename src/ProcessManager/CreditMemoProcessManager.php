<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ProcessManager;

use Prooph\ServiceBus\CommandBus;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\UnitsRefunded;

final class CreditMemoProcessManager
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(UnitsRefunded $event): void
    {
        $this->commandBus->dispatch(new GenerateCreditMemo(
            $event->orderNumber(),
            $event->amount(),
            $event->unitIds(),
            $event->shipmentIds(),
            ''
        ));
    }
}

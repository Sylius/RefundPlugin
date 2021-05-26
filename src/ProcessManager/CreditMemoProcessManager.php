<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ProcessManager;

use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreditMemoProcessManager implements UnitsRefundedProcessStepInterface
{
    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function next(UnitsRefunded $unitsRefunded): void
    {
        $this->commandBus->dispatch(new GenerateCreditMemo(
            $unitsRefunded->orderNumber(),
            $unitsRefunded->amount(),
            $unitsRefunded->units(),
            $unitsRefunded->shipments(),
            $unitsRefunded->comment()
        ));
    }
}

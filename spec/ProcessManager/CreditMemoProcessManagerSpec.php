<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\ProcessManager;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\CommandBus;
use Prophecy\Argument;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\UnitsRefunded;

final class CreditMemoProcessManagerSpec extends ObjectBehavior
{
    function let(CommandBus $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    function it_reacts_on_units_generated_event_and_dispatch_generate_credit_memo_command(CommandBus $commandBus)
    {
        $commandBus->dispatch(Argument::that(function (GenerateCreditMemo $command): bool {
            return
                $command->orderNumber() === '000222' &&
                $command->total() === 3000 &&
                $command->unitIds() === [1, 2, 3] &&
                $command->shipmentIds() === [1, 2]
            ;
        }));

        $this(new UnitsRefunded('000222', [1, 2, 3], [1, 2], 1, 3000, 'USDs'));
    }
}

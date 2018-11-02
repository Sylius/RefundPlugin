<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\ProcessManager;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreditMemoProcessManagerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    function it_reacts_on_units_generated_event_and_dispatch_generate_credit_memo_command(MessageBusInterface $commandBus)
    {
        $unitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];
        $shipmentRefunds = [new ShipmentRefund(1, 500), new ShipmentRefund(2, 1000)];

        $commandBus->dispatch(Argument::that(function (GenerateCreditMemo $command) use ($unitRefunds, $shipmentRefunds): bool {
            return
                $command->orderNumber() === '000222' &&
                $command->total() === 3000 &&
                $command->units() === $unitRefunds &&
                $command->shipments() === $shipmentRefunds &&
                $command->comment() === 'Comment'
            ;
        }));

        $this(new UnitsRefunded('000222', $unitRefunds, $shipmentRefunds, 1, 3000, 'USD', 'Comment'));
    }
}

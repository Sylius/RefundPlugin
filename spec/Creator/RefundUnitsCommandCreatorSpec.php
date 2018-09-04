<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Creator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\CommandCreatorInterface;
use Sylius\RefundPlugin\Model\UnitRefund;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsCommandCreatorSpec extends ObjectBehavior
{
    function let(
        RemainingTotalProviderInterface $remainingOrderItemUnitTotalProvider
    ): void {
        $this->beConstructedWith($remainingOrderItemUnitTotalProvider);
    }

    function it_implements_command_creator_interface(): void
    {
        $this->shouldImplement(CommandCreatorInterface::class);
    }

    function it_creates_refund_units_command_from_request_with_full_prices(
        RemainingTotalProviderInterface $remainingOrderItemUnitTotalProvider,
        Request $request
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                ['id' => '1'],
                ['id' => '2'],
            ],
            'sylius_refund_shipments' => [1],
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $remainingOrderItemUnitTotalProvider->getTotalLeftToRefund(1)->willReturn(1000);
        $remainingOrderItemUnitTotalProvider->getTotalLeftToRefund(2)->willReturn(3000);

        $this->fromRequest($request)->shouldReturnCommand(new RefundUnits(
            '00001111',
            [new UnitRefund(1, 1000), new UnitRefund(2, 3000)],
            [1],
            1,
            'Comment'
        ));
    }

    function it_creates_refund_units_command_from_request_with_partial_prices(
        RemainingTotalProviderInterface $remainingOrderItemUnitTotalProvider,
        Request $request
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                ['partial-id' => '1', 'amount' => '10.00'],
                ['id' => '2'],
            ],
            'sylius_refund_shipments' => [1],
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $remainingOrderItemUnitTotalProvider->getTotalLeftToRefund(2)->willReturn(3000);

        $this->fromRequest($request)->shouldReturnCommand(new RefundUnits(
            '00001111',
            [new UnitRefund(1, 1000), new UnitRefund(2, 3000)],
            [1],
            1,
            'Comment'
        ));
    }

    function it_throws_exception_if_there_is_no_units_nor_shipments_provided(Request $request): void
    {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag(['sylius_refund_payment_method' => 1]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('fromRequest', [$request])
        ;
    }

    function it_throws_exception_if_there_is_no_order_number_provided(Request $request): void
    {
        $request->attributes = new ParameterBag([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('fromRequest', [$request])
        ;
    }

    public function getMatchers(): array
    {
        return [
            'returnCommand' => function (RefundUnits $command, RefundUnits $expectedCommand): bool {
                return
                    $command->orderNumber() === $expectedCommand->orderNumber() &&
                    $command->units() == $expectedCommand->units() &&
                    $command->shipments() === $expectedCommand->shipments() &&
                    $command->paymentMethodId() === $expectedCommand->paymentMethodId() &&
                    $command->comment() === $expectedCommand->comment()
                ;
            },
        ];
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Creator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\RefundUnitsCommandCreatorInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsCommandCreatorSpec extends ObjectBehavior
{
    function let(
        RemainingTotalProviderInterface $remainingTotalProvider
    ): void {
        $this->beConstructedWith($remainingTotalProvider);
    }

    function it_implements_refund_units_command_creator_interface(): void
    {
        $this->shouldImplement(RefundUnitsCommandCreatorInterface::class);
    }

    function it_creates_refund_units_command_from_request_with_full_prices(
        RemainingTotalProviderInterface $remainingTotalProvider,
        Request $request
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                ['id' => '1'],
                ['id' => '2'],
            ],
            'sylius_refund_shipments' => [['id' => 1]],
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $remainingTotalProvider->getTotalLeftToRefund(1, RefundType::orderItemUnit())->willReturn(1000);
        $remainingTotalProvider->getTotalLeftToRefund(2, RefundType::orderItemUnit())->willReturn(3000);
        $remainingTotalProvider->getTotalLeftToRefund(1, RefundType::shipment())->willReturn(5000);

        $this->fromRequest($request)->shouldReturnCommand(new RefundUnits(
            '00001111',
            [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)],
            [new ShipmentRefund(1, 5000)],
            1,
            'Comment'
        ));
    }

    function it_creates_refund_units_command_from_request_with_partial_prices(
        RemainingTotalProviderInterface $remainingTotalProvider,
        Request $request
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                ['partial-id' => '1', 'amount' => '10.00'],
                ['id' => '2'],
            ],
            'sylius_refund_shipments' => [['partial-id' => 1, 'amount' => '5.00']],
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $remainingTotalProvider->getTotalLeftToRefund(2, RefundType::orderItemUnit())->willReturn(3000);

        $this->fromRequest($request)->shouldReturnCommand(new RefundUnits(
            '00001111',
            [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)],
            [new ShipmentRefund(1, 500)],
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
                    $command->shipments() == $expectedCommand->shipments() &&
                    $command->paymentMethodId() === $expectedCommand->paymentMethodId() &&
                    $command->comment() === $expectedCommand->comment()
                ;
            },
        ];
    }
}

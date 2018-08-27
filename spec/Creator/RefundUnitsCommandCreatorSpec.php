<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Creator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\CommandCreatorInterface;
use Sylius\RefundPlugin\Model\UnitRefund;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsCommandCreatorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $orderItemUnitRepository): void
    {
        $this->beConstructedWith($orderItemUnitRepository);
    }

    function it_implements_command_creator_interface(): void
    {
        $this->shouldImplement(CommandCreatorInterface::class);
    }

    function it_creates_refund_units_command_from_request_with_full_prices(
        RepositoryInterface $orderItemUnitRepository,
        Request $request,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                ['id' => 1],
                ['id' => 2]
            ],
            'sylius_refund_shipments' => [1],
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $orderItemUnitRepository->find(1)->willReturn($firstOrderItemUnit);
        $firstOrderItemUnit->getTotal()->willReturn(1000);

        $orderItemUnitRepository->find(2)->willReturn($secondOrderItemUnit);
        $secondOrderItemUnit->getTotal()->willReturn(3000);

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
                    $command->shipmentIds() === $expectedCommand->shipmentIds() &&
                    $command->paymentMethodId() === $expectedCommand->paymentMethodId() &&
                    $command->comment() === $expectedCommand->comment()
                ;
            },
        ];
    }
}

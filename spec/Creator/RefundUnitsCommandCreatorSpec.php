<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Creator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\RefundUnitsCommandCreatorInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsCommandCreatorSpec extends ObjectBehavior
{
    public function let(
        UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator
    ): void {
        $this->beConstructedWith($unitRefundTotalCalculator);
    }

    public function it_implements_refund_units_command_creator_interface(): void
    {
        $this->shouldImplement(RefundUnitsCommandCreatorInterface::class);
    }

    public function it_creates_refund_units_command_from_request_with_full_prices(
        UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator,
        Request $request
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                1 => ['full' => 'on'],
                2 => ['full' => 'on'],
            ],
            'sylius_refund_shipments' => [
                1 => ['full' => 'on'],
            ],
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(1, RefundType::orderItemUnit(), null)->willReturn(1000);
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(2, RefundType::orderItemUnit(), null)->willReturn(3000);
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(1, RefundType::shipment(), null)->willReturn(5000);

        $this->fromRequest($request)->shouldReturnCommand(new RefundUnits(
            '00001111',
            [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)],
            [new ShipmentRefund(1, 5000)],
            1,
            'Comment'
        ));
    }

    public function it_creates_refund_units_command_from_request_with_partial_prices(
        UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator,
        Request $request
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_units' => [
                1 => ['amount' => '10.00'],
                2 => ['full' => 'on'],
            ],
            'sylius_refund_shipments' => [
                1 => ['amount' => '5.00'],
            ],
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(1, RefundType::orderItemUnit(), 10.00)->willReturn(1000);
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(2, RefundType::orderItemUnit(), null)->willReturn(3000);
        $unitRefundTotalCalculator->calculateForUnitWithIdAndType(1, RefundType::shipment(), 5.00)->willReturn(500);

        $this->fromRequest($request)->shouldReturnCommand(new RefundUnits(
            '00001111',
            [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(2, 3000)],
            [new ShipmentRefund(1, 500)],
            1,
            'Comment'
        ));
    }

    public function it_throws_exception_if_there_is_no_units_nor_shipments_provided(Request $request): void
    {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag(['sylius_refund_payment_method' => 1]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('fromRequest', [$request])
        ;
    }

    public function it_throws_exception_if_there_is_no_order_number_provided(Request $request): void
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

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
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Creator\RequestCommandCreatorInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsCommandCreatorSpec extends ObjectBehavior
{
    function let(RequestToRefundUnitsConverterInterface $refundUnitsConverter): void
    {
        $this->beConstructedWith($refundUnitsConverter);
    }

    function it_implements_refund_units_command_creator_interface(): void
    {
        $this->shouldImplement(RequestCommandCreatorInterface::class);
    }

    function it_creates_refund_units_command_from_request(
        RequestToRefundUnitsConverterInterface $refundUnitsConverter,
        UnitRefundInterface $firstUnitRefund,
        UnitRefundInterface $secondUnitRefund,
        UnitRefundInterface $shipmentRefund,
        Request $request,
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag([
            'sylius_refund_payment_method' => 1,
            'sylius_refund_comment' => 'Comment',
        ]);

        $refundUnitsConverter->convert($request)->willReturn([$firstUnitRefund, $secondUnitRefund, $shipmentRefund]);

        $this->fromRequest($request)->shouldReturnCommand(new RefundUnits(
            '00001111',
            [$firstUnitRefund->getWrappedObject(), $secondUnitRefund->getWrappedObject(), $shipmentRefund->getWrappedObject()],
            1,
            'Comment',
        ));
    }

    function it_throws_exception_if_there_is_no_units_nor_shipments_provided(
        RequestToRefundUnitsConverterInterface $refundUnitsConverter,
        Request $request,
    ): void {
        $request->attributes = new ParameterBag(['orderNumber' => '00001111']);
        $request->request = new ParameterBag(['sylius_refund_payment_method' => 1]);

        $refundUnitsConverter->convert($request)->willReturn([]);

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

    function getMatchers(): array
    {
        return [
            'returnCommand' => function (RefundUnits $command, RefundUnits $expectedCommand): bool {
                return
                    $command->orderNumber() === $expectedCommand->orderNumber() &&
                    $command->units() == $expectedCommand->units() &&
                    $command->paymentMethodId() === $expectedCommand->paymentMethodId() &&
                    $command->comment() === $expectedCommand->comment()
                ;
            },
        ];
    }
}

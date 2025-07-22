<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit\CommandHandler;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\CommandHandler\RefundUnitsHandler;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefunding;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundUnitsHandlerTest extends TestCase
{
    private RefunderInterface $orderItemUnitsRefunder;
    private RefunderInterface $orderShipmentsRefunder;
    private MessageBusInterface $eventBus;
    private OrderRepositoryInterface $orderRepository;
    private RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator;
    private RefundUnitsHandler $handler;

    protected function setUp(): void
    {
        $this->orderItemUnitsRefunder = $this->createMock(RefunderInterface::class);
        $this->orderShipmentsRefunder = $this->createMock(RefunderInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->refundUnitsCommandValidator = $this->createMock(RefundUnitsCommandValidatorInterface::class);
        
        $this->handler = new RefundUnitsHandler(
            [$this->orderItemUnitsRefunder, $this->orderShipmentsRefunder],
            $this->eventBus,
            $this->orderRepository,
            $this->refundUnitsCommandValidator
        );
    }

    /** @test */
    function it_handles_command_and_create_refund_for_each_refunded_unit(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $unitRefunds = [
            new OrderItemUnitRefund(1, 3000),
            new OrderItemUnitRefund(3, 4000),
            new ShipmentRefund(3, 500),
            new ShipmentRefund(4, 1000),
        ];

        $this->orderItemUnitsRefunder
            ->expects($this->once())
            ->method('refundFromOrder')
            ->with($unitRefunds, '000222')
            ->willReturn(3000);

        $this->orderShipmentsRefunder
            ->expects($this->once())
            ->method('refundFromOrder')
            ->with($unitRefunds, '000222')
            ->willReturn(4000);

        $this->orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn($order);

        $order
            ->expects($this->once())
            ->method('getCurrencyCode')
            ->willReturn('USD');

        $this->refundUnitsCommandValidator
            ->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf(RefundUnits::class));

        $event = new UnitsRefunded(
            '000222',
            $unitRefunds,
            1,
            7000,
            'USD',
            'Comment'
        );
        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $this->handler->__invoke(new RefundUnits('000222', $unitRefunds, 1, 'Comment'));
    }

    /** @test */
    function it_throws_an_exception_if_order_is_not_available_for_refund(): void
    {
        $refundUnitsCommand = new RefundUnits(
            '000222',
            [
                new OrderItemUnitRefund(1, 3000),
                new OrderItemUnitRefund(3, 4000),
                new ShipmentRefund(3, 500),
                new ShipmentRefund(4, 1000)
            ],
            1,
            'Comment'
        );

        $this->refundUnitsCommandValidator
            ->expects($this->once())
            ->method('validate')
            ->with($refundUnitsCommand)
            ->willThrowException(OrderNotAvailableForRefunding::withOrderNumber('000222'));

        $this->expectException(OrderNotAvailableForRefunding::class);
        $this->handler->__invoke($refundUnitsCommand);
    }
}
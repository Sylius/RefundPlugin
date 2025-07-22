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

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\CommandHandler\GenerateCreditMemoHandler;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class GenerateCreditMemoHandlerTest extends TestCase
{
    private CreditMemoGeneratorInterface $creditMemoGenerator;
    private EntityManagerInterface $creditMemoManager;
    private MessageBusInterface $eventBus;
    private OrderRepositoryInterface $orderRepository;
    private CreditMemoFileResolverInterface $creditMemoFileResolver;
    private GenerateCreditMemoHandler $handler;

    protected function setUp(): void
    {
        $this->creditMemoGenerator = $this->createMock(CreditMemoGeneratorInterface::class);
        $this->creditMemoManager = $this->createMock(EntityManagerInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->creditMemoFileResolver = $this->createMock(CreditMemoFileResolverInterface::class);
        
        $this->handler = new GenerateCreditMemoHandler(
            $this->creditMemoGenerator,
            $this->creditMemoManager,
            $this->eventBus,
            $this->orderRepository,
            $this->creditMemoFileResolver,
            true
        );
    }

    /** @test */
    function it_generates_credit_memo_with_a_pdf_file(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $refundUnits = [
            new OrderItemUnitRefund(1, 1000),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(3, 1000),
        ];

        $this->orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000666')
            ->willReturn($order);

        $this->creditMemoGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($order, 7000, $refundUnits, 'Comment')
            ->willReturn($creditMemo);

        $creditMemo
            ->expects($this->once())
            ->method('getNumber')
            ->willReturn('2018/01/000001');

        $this->creditMemoManager
            ->expects($this->once())
            ->method('persist')
            ->with($creditMemo);

        $this->creditMemoManager
            ->expects($this->once())
            ->method('flush');

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $this->creditMemoFileResolver
            ->expects($this->once())
            ->method('resolveByCreditMemo')
            ->with($creditMemo)
            ->willReturn($creditMemoPdf);

        $event = new CreditMemoGenerated('2018/01/000001', '000666');
        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $this->handler->__invoke(new GenerateCreditMemo('000666', 7000, $refundUnits, 'Comment'));
    }

    /** @test */
    function it_generates_only_credit_memo_without_a_pdf_file(): void
    {
        $handler = new GenerateCreditMemoHandler(
            $this->creditMemoGenerator,
            $this->creditMemoManager,
            $this->eventBus,
            $this->orderRepository,
            $this->creditMemoFileResolver,
            false
        );

        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $refundUnits = [
            new OrderItemUnitRefund(1, 1000),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(3, 1000),
        ];

        $this->orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000666')
            ->willReturn($order);

        $this->creditMemoGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($order, 7000, $refundUnits, 'Comment')
            ->willReturn($creditMemo);

        $creditMemo
            ->expects($this->once())
            ->method('getNumber')
            ->willReturn('2018/01/000001');

        $this->creditMemoManager
            ->expects($this->once())
            ->method('persist')
            ->with($creditMemo);

        $this->creditMemoManager
            ->expects($this->once())
            ->method('flush');

        $this->creditMemoFileResolver
            ->expects($this->never())
            ->method('resolveByCreditMemo');

        $event = new CreditMemoGenerated('2018/01/000001', '000666');
        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $handler->__invoke(new GenerateCreditMemo('000666', 7000, $refundUnits, 'Comment'));
    }

    /** @test */
    function it_generates_only_credit_memo_without_a_pdf_file_if_pdf_generation_is_disabled(): void
    {
        $handler = new GenerateCreditMemoHandler(
            $this->creditMemoGenerator,
            $this->creditMemoManager,
            $this->eventBus,
            $this->orderRepository,
            $this->creditMemoFileResolver,
            false
        );

        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $refundUnits = [
            new OrderItemUnitRefund(1, 1000),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(3, 1000),
        ];

        $this->orderRepository
            ->expects($this->once())
            ->method('findOneByNumber')
            ->with('000666')
            ->willReturn($order);

        $this->creditMemoGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($order, 7000, $refundUnits, 'Comment')
            ->willReturn($creditMemo);

        $creditMemo
            ->expects($this->once())
            ->method('getNumber')
            ->willReturn('2018/01/000001');

        $this->creditMemoManager
            ->expects($this->once())
            ->method('persist')
            ->with($creditMemo);

        $this->creditMemoManager
            ->expects($this->once())
            ->method('flush');

        $this->creditMemoFileResolver
            ->expects($this->never())
            ->method('resolveByCreditMemo');

        $event = new CreditMemoGenerated('2018/01/000001', '000666');
        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $handler->__invoke(new GenerateCreditMemo('000666', 7000, $refundUnits, 'Comment'));
    }
}
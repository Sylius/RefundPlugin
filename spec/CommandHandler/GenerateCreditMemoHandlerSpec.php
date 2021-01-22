<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class GenerateCreditMemoHandlerSpec extends ObjectBehavior
{
    function let(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->beConstructedWith($creditMemoGenerator, $creditMemoManager, $eventBus, $orderRepository);
    }

    function it_generates_credit_memo(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        CreditMemoInterface $creditMemo,
        OrderInterface $order
    ): void {
        $orderItemUnitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];
        $shipmentRefunds = [new ShipmentRefund(3, 1000)];

        $orderRepository->findOneByNumber('000666')->willReturn($order);

        $creditMemoGenerator->generate($order, 7000, $orderItemUnitRefunds, $shipmentRefunds, 'Comment')->willReturn($creditMemo);

        $creditMemo->getNumber()->willReturn('2018/01/000001');

        $creditMemoManager->persist($creditMemo)->shouldBeCalled();
        $creditMemoManager->flush()->shouldBeCalled();

        $event = new CreditMemoGenerated('2018/01/000001', '000666');
        $eventBus->dispatch($event)->willReturn(new Envelope($event))->shouldBeCalled();

        $this(new GenerateCreditMemo('000666', 7000, $orderItemUnitRefunds, $shipmentRefunds, 'Comment'));
    }
}

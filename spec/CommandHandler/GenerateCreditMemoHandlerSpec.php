<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Exception\OrderNotFound;

final class GenerateCreditMemoHandlerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, ObjectManager $creditMemoManager, EventBus $eventBus)
    {
        $this->beConstructedWith($orderRepository, $creditMemoManager, $eventBus);
    }

    function it_generates_credit_memo(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $creditMemoManager,
        EventBus $eventBus,
        OrderInterface $order
    ): void {
        $orderRepository->findOneByNumber('000666')->willReturn($order);
        $order->getCurrencyCode()->willReturn('GBP');

        $creditMemoManager->persist(Argument::that(function (CreditMemo $creditMemo): bool {
            return
                $creditMemo->getOrderNumber() === '000666' &&
                $creditMemo->getTotal() === 1000 &&
                $creditMemo->getCurrencyCode() === 'GBP'
            ;
        }))->shouldBeCalled();
        $creditMemoManager->flush()->shouldBeCalled();

        $eventBus->dispatch(Argument::that(function(CreditMemoGenerated $event): bool {
            return $event->orderNumber() === '000666';
        }))->shouldBeCalled();

        $this(new GenerateCreditMemo('000666', 1000));
    }

    function it_throws_exception_if_order_with_given_number_does_not_exist(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneByNumber('000666')->willReturn(null);

        $this
            ->shouldThrow(OrderNotFound::class)
            ->during('__invoke', [new GenerateCreditMemo('000666', 1000)])
        ;
    }
}

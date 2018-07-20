<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\OrderNotFound;

final class GenerateCreditMemoHandlerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, ObjectManager $creditMemoManager)
    {
        $this->beConstructedWith($orderRepository, $creditMemoManager);
    }

    function it_generates_credit_memo(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $creditMemoManager,
        OrderInterface $order
    ): void {
        $orderRepository->findOneByNumber('000666')->willReturn($order);
        $order->getCurrencyCode()->willReturn('GBP');

        $creditMemoManager->persist(Argument::that(function(CreditMemoInterface $creditMemo): bool {
            return
                $creditMemo->getOrderNumber() === '000666' &&
                $creditMemo->getTotal() === 1000 &&
                $creditMemo->getCurrencyCode() === 'GBP'
            ;
        }));

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

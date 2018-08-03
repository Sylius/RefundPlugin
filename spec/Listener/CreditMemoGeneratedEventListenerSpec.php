<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;

final class CreditMemoGeneratedEventListenerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $creditMemoRepository,
        OrderRepositoryInterface $orderRepository,
        CreditMemoEmailSenderInterface $creditMemoEmailSender
    ): void {
        $this->beConstructedWith($creditMemoRepository, $orderRepository, $creditMemoEmailSender);
    }

    function it_sends_an_email_to_customer_for_whose_order_credit_memo_was_generated(
        RepositoryInterface $creditMemoRepository,
        OrderRepositoryInterface $orderRepository,
        CreditMemoEmailSenderInterface $creditMemoEmailSender,
        CreditMemoInterface $creditMemo,
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $creditMemoRepository->findOneBy(['number' => '2018/04/00001111'])->willReturn($creditMemo);
        $orderRepository->findOneByNumber('000000001')->willReturn($order);

        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('john@example.com');

        $creditMemoEmailSender->send($creditMemo, 'john@example.com')->shouldBeCalled();

        $this->__invoke(new CreditMemoGenerated('2018/04/00001111', '000000001'));
    }

    function it_throws_exception_if_there_is_no_credit_memo_with_given_number(
        RepositoryInterface $creditMemoRepository
    ): void {
        $creditMemoRepository->findOneBy(['number' => '2018/04/00001111'])->willReturn(null);

        $this
            ->shouldThrow(CreditMemoNotFound::withNumber('2018/04/00001111'))
            ->during('__invoke', [new CreditMemoGenerated('2018/04/00001111', '000000001')])
        ;
    }

    function it_throws_exception_if_there_is_no_order_with_given_number(
        RepositoryInterface $creditMemoRepository,
        OrderRepositoryInterface $orderRepository,
        CreditMemoInterface $creditMemo
    ): void {
        $creditMemoRepository->findOneBy(['number' => '2018/04/00001111'])->willReturn($creditMemo);
        $orderRepository->findOneByNumber('000000001')->willReturn(null);

        $this
            ->shouldThrow(OrderNotFound::withOrderNumber('000000001'))
            ->during('__invoke', [new CreditMemoGenerated('2018/04/00001111', '000000001')])
        ;
    }
}

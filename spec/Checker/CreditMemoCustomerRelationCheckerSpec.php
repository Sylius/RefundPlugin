<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationChecker;
use Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationCheckerInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotAccessible;

final class CreditMemoCustomerRelationCheckerSpec extends ObjectBehavior
{
    function let(
        CustomerContextInterface $customerContext,
        RepositoryInterface $creditMemoRepository,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith($customerContext, $creditMemoRepository, $orderRepository);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CreditMemoCustomerRelationChecker::class);
    }

    function it_implements_credit_memo_customer_relation_checker_interface(): void
    {
        $this->shouldImplement(CreditMemoCustomerRelationCheckerInterface::class);
    }

    function it_checks_if_customer_id_from_order_is_equal_to_customer_id_from_customer_context(
        CustomerContextInterface $customerContext,
        RepositoryInterface $creditMemoRepository,
        OrderRepositoryInterface $orderRepository,
        CreditMemoInterface $creditMemo,
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $creditMemoRepository->find('00001')->willReturn($creditMemo);

        $creditMemo->getOrderNumber()->willReturn('00002');

        $orderRepository->findOneByNumber('00002')->willReturn($order);

        $order->getCustomer()->willReturn($customer);
        $customerContext->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn(1);

        $this->check('00001');
    }

    function it_throws_exception_if_customer_id_from_order_is_not_equal_to_id_from_context(
        CustomerContextInterface $customerContext,
        RepositoryInterface $creditMemoRepository,
        OrderRepositoryInterface $orderRepository,
        CreditMemoInterface $creditMemo,
        OrderInterface $order,
        CustomerInterface $firstCustomer,
        CustomerInterface $secondCustomer
    ): void {
        $creditMemoRepository->find('00001')->willReturn($creditMemo);

        $creditMemo->getOrderNumber()->willReturn('00002');

        $orderRepository->findOneByNumber('00002')->willReturn($order);

        $order->getCustomer()->willReturn($firstCustomer);
        $customerContext->getCustomer()->willReturn($secondCustomer);

        $firstCustomer->getId()->willReturn(1);
        $secondCustomer->getId()->willReturn(2);

        $this->shouldThrow(CreditMemoNotAccessible::class)->during('check', ['00001']);
    }
}

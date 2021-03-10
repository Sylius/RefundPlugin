<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\StateResolver;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;

final class OrderFullyRefundedStateResolverSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $stateMachineFactory,
        ObjectManager $orderManager,
        OrderFullyRefundedTotalCheckerInterface $orderFullyRefundedTotalChecker,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith(
            $stateMachineFactory,
            $orderManager,
            $orderFullyRefundedTotalChecker,
            $orderRepository
        );
    }

    function it_applies_refund_transition_on_order(
        OrderRepositoryInterface $orderRepository,
        OrderFullyRefundedTotalCheckerInterface $orderFullyRefundedTotalChecker,
        FactoryInterface $stateMachineFactory,
        ObjectManager $orderManager,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $orderFullyRefundedTotalChecker->isOrderFullyRefunded($order)->willReturn(true);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REFUND)->shouldBeCalled();

        $orderManager->flush()->shouldBeCalled();

        $this->resolve('000222');
    }

    function it_does_nothing_if_order_state_is_fully_refunded(
        OrderRepositoryInterface $orderRepository,
        OrderFullyRefundedTotalCheckerInterface $orderFullyRefundedTotalChecker,
        FactoryInterface $stateMachineFactory,
        OrderInterface $order
    ): void {
        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $orderFullyRefundedTotalChecker->isOrderFullyRefunded($order)->willReturn(true);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_REFUNDED);

        $stateMachineFactory->get(Argument::any())->shouldNotBeCalled();

        $this->resolve('000222');
    }

    function it_does_nothing_if_order_is_not_fully_refunded(
        OrderRepositoryInterface $orderRepository,
        OrderFullyRefundedTotalCheckerInterface $orderFullyRefundedTotalChecker,
        FactoryInterface $stateMachineFactory,
        OrderInterface $order
    ): void {
        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $orderFullyRefundedTotalChecker->isOrderFullyRefunded($order)->willReturn(false);

        $stateMachineFactory->get(Argument::any())->shouldNotBeCalled();

        $this->resolve('000222');
    }

    function it_throws_an_exception_if_there_is_no_order_with_given_number(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneByNumber('000222')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('resolve', ['000222'])
        ;
    }
}

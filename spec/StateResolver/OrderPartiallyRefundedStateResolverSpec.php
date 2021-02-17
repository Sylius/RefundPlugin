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
use Sylius\RefundPlugin\Exception\OrderNotFound;

final class OrderPartiallyRefundedStateResolverSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $stateMachineFactory,
        ObjectManager $orderManager
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachineFactory, $orderManager);
    }

    function it_marks_order_as_partially_refunded(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $stateMachineFactory,
        ObjectManager $orderManager,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneByNumber('000777')->willReturn($order);

        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)->shouldBeCalled();

        $orderManager->flush()->shouldBeCalled();

        $this->resolve('000777');
    }

    function it_does_nothing_if_order_is_already_marked_as_partially_refunded(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $stateMachineFactory,
        OrderInterface $order
    ): void {
        $orderRepository->findOneByNumber('000777')->willReturn($order);

        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PARTIALLY_REFUNDED);

        $stateMachineFactory->get(Argument::any())->shouldNotBeCalled();

        $this->resolve('000777');
    }

    function it_throws_exception_if_there_is_no_order_with_given_number(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneByNumber('000777')->willReturn(null);

        $this
            ->shouldThrow(OrderNotFound::withNumber('000777'))
            ->during('resolve', ['000777'])
        ;
    }
}

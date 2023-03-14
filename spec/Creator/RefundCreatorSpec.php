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

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefunded;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundCreatorSpec extends ObjectBehavior
{
    function let(
        RefundFactoryInterface $refundFactory,
        RemainingTotalProviderInterface $remainingTotalProvider,
        OrderRepositoryInterface $orderRepository,
        ObjectManager $refundEntityManager,
    ): void {
        $this->beConstructedWith(
            $refundFactory,
            $remainingTotalProvider,
            $orderRepository,
            $refundEntityManager,
        );
    }

    function it_implements_refund_creator_interface(): void
    {
        $this->shouldImplement(RefundCreatorInterface::class);
    }

    function it_creates_refund_with_given_data_and_save_it_in_database(
        RefundFactoryInterface $refundFactory,
        RemainingTotalProviderInterface $remainingTotalProvider,
        OrderRepositoryInterface $orderRepository,
        ObjectManager $refundEntityManager,
        OrderInterface $order,
        RefundInterface $refund,
    ): void {
        $refundType = RefundType::shipment();

        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $remainingTotalProvider->getTotalLeftToRefund(1, $refundType)->willReturn(1000);

        $refundFactory->createWithData($order, 1, 1000, RefundType::shipment())->willReturn($refund);

        $refundEntityManager->persist($refund)->shouldBeCalled();
        $refundEntityManager->flush()->shouldBeCalled();

        $this('000222', 1, 1000, $refundType);
    }

    function it_throws_an_exception_if_order_with_given_number_does_not_exist(
        OrderRepositoryInterface $orderRepository,
    ): void {
        $refundType = RefundType::shipment();

        $orderRepository->findOneByNumber('000222')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', ['000222', 1, 1000, $refundType])
        ;
    }

    function it_throws_exception_if_unit_has_already_been_refunded(
        OrderRepositoryInterface $orderRepository,
        RemainingTotalProviderInterface $remainingTotalProvider,
        OrderInterface $order,
    ): void {
        $refundType = RefundType::shipment();

        $orderRepository->findOneByNumber('000222')->willReturn($order);
        $remainingTotalProvider->getTotalLeftToRefund(1, $refundType)->willReturn(0);

        $this
            ->shouldThrow(UnitAlreadyRefunded::class)
            ->during('__invoke', ['000222', 1, 1000, $refundType])
        ;
    }
}

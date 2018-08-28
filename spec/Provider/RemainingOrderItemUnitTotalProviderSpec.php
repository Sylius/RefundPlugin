<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RemainingOrderItemUnitTotalProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $orderItemUnitRepository, RepositoryInterface $refundRepository): void
    {
        $this->beConstructedWith($orderItemUnitRepository, $refundRepository);
    }

    function it_implements_remaining_total_provider_interface(): void
    {
        $this->shouldImplement(RemainingTotalProviderInterface::class);
    }

    function it_returns_unit_remaining_total_to_refund(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $refundRepository,
        OrderItemUnitInterface $orderItemUnit,
        RefundInterface $refund
    ): void {
        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => RefundType::orderItemUnit()->__toString()])
            ->willReturn([$refund])
        ;

        $refund->getAmount()->willReturn(500);
        $orderItemUnit->getTotal()->willReturn(1000);

        $this->getTotalLeftToRefund(1)->shouldReturn(500);
    }

    function it_returns_unit_total_if_there_is_no_refund_for_this_unit_yet(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $refundRepository,
        OrderItemUnitInterface $orderItemUnit
    ): void {
        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => RefundType::orderItemUnit()->__toString()])
            ->willReturn([])
        ;

        $orderItemUnit->getTotal()->willReturn(1000);

        $this->getTotalLeftToRefund(1)->shouldReturn(1000);
    }

    function it_throws_exception_if_there_is_no_order_with_given_id(RepositoryInterface $orderItemUnitRepository): void
    {
        $orderItemUnitRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getTotalLeftToRefund', [1])
        ;
    }
}

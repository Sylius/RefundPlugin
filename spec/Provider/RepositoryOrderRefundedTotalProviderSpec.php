<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class RepositoryOrderRefundedTotalProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $refundRepository, RepositoryInterface $orderItemUnitRepository): void
    {
        $this->beConstructedWith($refundRepository, $orderItemUnitRepository);
    }

    function it_implements_order_refunded_total_provider_interface(): void
    {
        $this->shouldImplement(OrderRefundedTotalProviderInterface::class);
    }

    function it_returns_refunded_total_of_order_with_given_number(
        RepositoryInterface $refundRepository,
        RepositoryInterface $orderItemUnitRepository,
        RefundInterface $firstRefund,
        RefundInterface $secondRefund,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit
    ): void {
        $refundRepository->findBy(['orderNumber' => '000222'])->willReturn([$firstRefund, $secondRefund]);

        $firstRefund->getRefundedUnitId()->willReturn(10);
        $secondRefund->getRefundedUnitId()->willReturn(5);

        $orderItemUnitRepository->find(10)->willReturn($firstOrderItemUnit);
        $orderItemUnitRepository->find(5)->willReturn($secondOrderItemUnit);

        $firstOrderItemUnit->getTotal()->willReturn(1000);
        $secondOrderItemUnit->getTotal()->willReturn(500);

        $this->__invoke('000222')->shouldReturn(1500);
    }
}

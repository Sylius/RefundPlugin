<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class RepositoryRefundedUnitTotalProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $orderItemUnitRepository): void
    {
        $this->beConstructedWith($orderItemUnitRepository);
    }

    function it_implements_refunded_unit_total_provider_interface(): void
    {
        $this->shouldImplement(RefundedUnitTotalProviderInterface::class);
    }

    function it_returns_total_of_order_item_unit(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $orderItemUnit
    ): void {
        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $orderItemUnit->getTotal()->willReturn(1000);

        $this->getTotalOfUnitWithId(1)->shouldReturn(1000);
    }

    function it_throws_exception_if_there_is_no_order_item_unit_with_given_id(RepositoryInterface $orderItemUnitRepository)
    {
        $orderItemUnitRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getTotalOfUnitWithId', [1])
        ;
    }
}

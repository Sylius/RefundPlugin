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

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;

final class OrderItemUnitTotalProviderSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $orderItemUnitRepository,
    ): void {
        $this->beConstructedWith($orderItemUnitRepository);
    }

    function it_is_refund_unit_total_provider(): void
    {
        $this->shouldImplement(RefundUnitTotalProviderInterface::class);
    }

    function it_returns_order_item_unit_total_to_refund(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $orderItemUnit,
    ): void {
        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $orderItemUnit->getTotal()->willReturn(1000);

        $this->getRefundUnitTotal(1)->shouldReturn(1000);
    }

    function it_throws_exception_if_there_is_no_order_item_unit_with_given_id(
        RepositoryInterface $orderItemUnitRepository,
    ): void {
        $orderItemUnitRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getRefundUnitTotal', [1])
        ;
    }
}

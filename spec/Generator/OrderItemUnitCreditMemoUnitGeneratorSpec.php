<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Generator\CreditMemoUnitGeneratorInterface;

final class OrderItemUnitCreditMemoUnitGeneratorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $orderItemUnitRepository): void
    {
        $this->beConstructedWith($orderItemUnitRepository);
    }

    function it_implements_credit_memo_unit_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoUnitGeneratorInterface::class);
    }

    function it_generates_credit_memo_unit_from_order_item_unit(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit
    ): void {
        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $orderItemUnit->getOrderItem()->willReturn($orderItem);

        $orderItem->getProductName()->willReturn('Portal gun');

        $orderItemUnit->getTotal()->willReturn(500);
        $orderItemUnit->getTaxTotal()->willReturn(50);

        $this->generate(1, 500)->shouldBeLike(new CreditMemoUnit('Portal gun', 500, 50));
    }

    function it_generates_credit_memo_unit_from_order_item_unit_with_partial_total_and_tax_total(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit
    ): void {
        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $orderItemUnit->getOrderItem()->willReturn($orderItem);

        $orderItem->getProductName()->willReturn('Portal gun');

        $orderItemUnit->getTotal()->willReturn(1500);
        $orderItemUnit->getTaxTotal()->willReturn(300);

        $this->generate(1, 500)->shouldBeLike(new CreditMemoUnit('Portal gun', 500, 100));
    }

    function it_throws_exception_if_there_is_no_order_item_unit_with_given_id(
        RepositoryInterface $orderItemUnitRepository
    ): void {
        $orderItemUnitRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('generate', [1, 500])
        ;
    }

    function it_throws_exception_if_passed_amount_is_more_than_order_item_unit_total(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $orderItemUnit
    ): void {
        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $orderItemUnit->getTotal()->willReturn(500);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('generate', [1, 1000])
        ;
    }
}

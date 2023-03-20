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

namespace spec\Sylius\RefundPlugin\Converter\LineItem;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;

final class OrderItemUnitLineItemsConverterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $orderItemUnitRepository, TaxRateProviderInterface $taxRateProvider): void
    {
        $this->beConstructedWith($orderItemUnitRepository, $taxRateProvider);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_converts_unit_refunds_to_line_items(
        RepositoryInterface $orderItemUnitRepository,
        TaxRateProviderInterface $taxRateProvider,
        OrderItemUnitInterface $orderItemUnit,
        OrderItemInterface $orderItem,
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 500);

        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);

        $orderItemUnit->getOrderItem()->willReturn($orderItem);
        $orderItemUnit->getTotal()->willReturn(1500);
        $orderItemUnit->getTaxTotal()->willReturn(300);

        $taxRateProvider->provide($orderItemUnit)->willReturn('25%');

        $orderItem->getProductName()->willReturn('Portal gun');

        $this->convert([$unitRefund])->shouldBeLike([new LineItem(
            'Portal gun',
            1,
            400,
            500,
            400,
            500,
            100,
            '25%',
        )]);
    }

    function it_throws_an_error_if_one_of_units_is_not_order_item_unit_refund(): void
    {
        $unitRefund = new OrderItemUnitRefund(1, 500);
        $shipmentRefund = new ShipmentRefund(3, 1500);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$unitRefund, $shipmentRefund]])
        ;
    }

    function it_groups_the_same_line_items_during_converting(
        RepositoryInterface $orderItemUnitRepository,
        TaxRateProviderInterface $taxRateProvider,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 500);
        $secondUnitRefund = new OrderItemUnitRefund(2, 960);
        $thirdUnitRefund = new OrderItemUnitRefund(2, 960);

        $orderItemUnitRepository->find(1)->willReturn($firstOrderItemUnit);

        $firstOrderItemUnit->getOrderItem()->willReturn($firstOrderItem);
        $firstOrderItemUnit->getTotal()->willReturn(1500);
        $firstOrderItemUnit->getTaxTotal()->willReturn(300);

        $taxRateProvider->provide($firstOrderItemUnit)->willReturn('25%');

        $firstOrderItem->getProductName()->willReturn('Portal gun');

        $orderItemUnitRepository->find(2)->willReturn($secondOrderItemUnit);

        $secondOrderItemUnit->getOrderItem()->willReturn($secondOrderItem);
        $secondOrderItemUnit->getTotal()->willReturn(960);
        $secondOrderItemUnit->getTaxTotal()->willReturn(160);

        $taxRateProvider->provide($secondOrderItemUnit)->willReturn('20%');

        $secondOrderItem->getProductName()->willReturn('Space gun');

        $this->convert([$firstUnitRefund, $secondUnitRefund, $thirdUnitRefund])->shouldBeLike([
            new LineItem(
                'Portal gun',
                1,
                400,
                500,
                400,
                500,
                100,
                '25%',
            ),
            new LineItem(
                'Space gun',
                2,
                800,
                960,
                1600,
                1920,
                320,
                '20%',
            ),
        ]);
    }

    function it_throws_an_exception_if_there_is_no_shipping_adjustment_with_given_id(
        RepositoryInterface $orderItemUnitRepository,
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 500);

        $orderItemUnitRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$unitRefund]])
        ;
    }

    function it_throws_an_exception_if_refund_amount_is_higher_than_shipping_amount(
        RepositoryInterface $orderItemUnitRepository,
        OrderItemUnitInterface $orderItemUnit,
    ): void {
        $unitRefund = new OrderItemUnitRefund(1, 1001);

        $orderItemUnitRepository->find(1)->willReturn($orderItemUnit);
        $orderItemUnit->getTotal()->willReturn(500);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('convert', [[$unitRefund]])
        ;
    }
}

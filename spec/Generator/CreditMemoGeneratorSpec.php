<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Generator\NumberGenerator;

final class CreditMemoGeneratorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        NumberGenerator $creditMemoNumberGenerator
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $orderItemUnitRepository,
            $adjustmentRepository,
            $creditMemoNumberGenerator
        );
    }

    function it_implements_credit_memo_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoGeneratorInterface::class);
    }

    function it_generates_credit_memo_basing_on_event_data(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        NumberGenerator $creditMemoNumberGenerator,
        OrderInterface $order,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit,
        AdjustmentInterface $shipment
    ): void {
        $orderRepository->findOneByNumber('000666')->willReturn($order);
        $order->getCurrencyCode()->willReturn('GBP');

        $orderItemUnitRepository->find(1)->willReturn($firstOrderItemUnit);
        $firstOrderItemUnit->getOrderItem()->willReturn($firstOrderItem);
        $firstOrderItemUnit->getTotal()->willReturn(500);
        $firstOrderItemUnit->getTaxTotal()->willReturn(50);
        $firstOrderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(0);
        $firstOrderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->willReturn(0);
        $firstOrderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn(0);
        $firstOrderItem->getProductName()->willReturn('Portal gun');

        $orderItemUnitRepository->find(2)->willReturn($secondOrderItemUnit);
        $secondOrderItemUnit->getOrderItem()->willReturn($secondOrderItem);
        $secondOrderItemUnit->getTotal()->willReturn(500);
        $secondOrderItemUnit->getTaxTotal()->willReturn(50);
        $secondOrderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(25);
        $secondOrderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->willReturn(25);
        $secondOrderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn(0);
        $secondOrderItem->getProductName()->willReturn('Broken Leg Serum');

        $adjustmentRepository->findOneBy(['id' => 3, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])->willReturn($shipment);
        $shipment->getLabel()->willReturn('Galaxy post');
        $shipment->getAmount()->willReturn(400);

        $creditMemoNumberGenerator->generate()->willReturn('2018/07/00001111');

        $this->generate('000666', 1400, [1, 2], [3])->shouldBeLike(new CreditMemo(
            '2018/07/00001111',
            '000666',
            1400,
            'GBP',
            [
                new CreditMemoUnit('Portal gun', 500, 50, 0),
                new CreditMemoUnit('Broken Leg Serum', 500, 50, 50),
                new CreditMemoUnit('Galaxy post', 400, 0, 0),
            ]
        ));
    }
}

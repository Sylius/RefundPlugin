<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoUnitGeneratorInterface;
use Sylius\RefundPlugin\Generator\NumberGenerator;

final class CreditMemoGeneratorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        NumberGenerator $creditMemoNumberGenerator
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $orderItemUnitCreditMemoUnitGenerator,
            $shipmentCreditMemoUnitGenerator,
            $creditMemoNumberGenerator
        );
    }

    function it_implements_credit_memo_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoGeneratorInterface::class);
    }

    function it_generates_credit_memo_basing_on_event_data(
        OrderRepositoryInterface $orderRepository,
        NumberGenerator $creditMemoNumberGenerator,
        OrderInterface $order,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator
    ): void {
        $orderRepository->findOneByNumber('000666')->willReturn($order);
        $order->getCurrencyCode()->willReturn('GBP');

        $firstCreditMemoUnit = new CreditMemoUnit('Portal gun', 500, 50, 0);
        $orderItemUnitCreditMemoUnitGenerator->generate(1)->willReturn($firstCreditMemoUnit);

        $secondCreditMemoUnit = new CreditMemoUnit('Broken Leg Serum', 500, 50, 50);
        $orderItemUnitCreditMemoUnitGenerator->generate(2)->willReturn($secondCreditMemoUnit);

        $shipmentCreditMemoUnit = new CreditMemoUnit('Galaxy post', 400, 0, 0);
        $shipmentCreditMemoUnitGenerator->generate(3)->willReturn($shipmentCreditMemoUnit);

        $creditMemoNumberGenerator->generate()->willReturn('2018/07/00001111');

        $this->generate('000666', 1400, [1, 2], [3])->shouldBeLike(new CreditMemo(
            '2018/07/00001111',
            '000666',
            1400,
            'GBP',
            [
                $firstCreditMemoUnit->serialize(),
                $secondCreditMemoUnit->serialize(),
                $shipmentCreditMemoUnit->serialize(),
            ]
        ));
    }

    function it_throws_exception_if_there_is_no_order_with_given_id(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneByNumber('000666')->willReturn(null);

        $this
            ->shouldThrow(OrderNotFound::withNumber('000666'))
            ->during('generate', ['000666', 1000, [], []])
        ;
    }
}

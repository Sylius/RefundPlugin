<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoChannel;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoUnitGeneratorInterface;
use Sylius\RefundPlugin\Generator\NumberGenerator;
use Sylius\RefundPlugin\Model\UnitRefund;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;

final class CreditMemoGeneratorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        NumberGenerator $creditMemoNumberGenerator,
        CurrentDateTimeProviderInterface $currentDateTimeProvider
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $orderItemUnitCreditMemoUnitGenerator,
            $shipmentCreditMemoUnitGenerator,
            $creditMemoNumberGenerator,
            $currentDateTimeProvider
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
        ChannelInterface $channel,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        \DateTime $dateTime
    ): void {
        $firstUnitRefund = new UnitRefund(1, 500);
        $secondUnitRefund = new UnitRefund(3, 500);

        $orderRepository->findOneByNumber('000666')->willReturn($order);
        $order->getCurrencyCode()->willReturn('GBP');
        $order->getLocaleCode()->willReturn('en_US');

        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB-US');
        $channel->getName()->willReturn('United States');

        $firstCreditMemoUnit = new CreditMemoUnit('Portal gun', 500, 50);
        $orderItemUnitCreditMemoUnitGenerator->generate(1, 500)->willReturn($firstCreditMemoUnit);

        $secondCreditMemoUnit = new CreditMemoUnit('Broken Leg Serum', 500, 50);
        $orderItemUnitCreditMemoUnitGenerator->generate(3, 500)->willReturn($secondCreditMemoUnit);

        $shipmentCreditMemoUnit = new CreditMemoUnit('Galaxy post', 400, 0);
        $shipmentCreditMemoUnitGenerator->generate(3)->willReturn($shipmentCreditMemoUnit);

        $creditMemoNumberGenerator->generate()->willReturn('2018/07/00001111');

        $currentDateTimeProvider->now()->willReturn($dateTime);

        $this->generate('000666', 1400, [$firstUnitRefund, $secondUnitRefund], [3], 'Comment')->shouldBeLike(new CreditMemo(
            '2018/07/00001111',
            '000666',
            1400,
            'GBP',
            'en_US',
            new CreditMemoChannel('WEB-US', 'United States'),
            [
                $firstCreditMemoUnit->serialize(),
                $secondCreditMemoUnit->serialize(),
                $shipmentCreditMemoUnit->serialize(),
            ],
            'Comment',
            $dateTime->getWrappedObject()
        ));
    }

    function it_throws_exception_if_there_is_no_order_with_given_id(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneByNumber('000666')->willReturn(null);

        $this
            ->shouldThrow(OrderNotFound::withNumber('000666'))
            ->during('generate', ['000666', 1000, [], [], 'Comment'])
        ;
    }
}

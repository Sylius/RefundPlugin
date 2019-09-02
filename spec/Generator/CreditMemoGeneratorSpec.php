<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoChannel;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Factory\CreditMemoFactoryInterface;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactoryInterface;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoUnitGeneratorInterface;
use Sylius\RefundPlugin\Generator\NumberGenerator;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;

final class CreditMemoGeneratorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        NumberGenerator $creditMemoNumberGenerator,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        CreditMemoIdentifierGeneratorInterface $creditMemoIdentifierGenerator,
        CreditMemoFactoryInterface $creditMemoFactory,
        CustomerBillingDataFactoryInterface $customerBillingDataFactory,
        ShopBillingDataFactoryInterface $shopBillingDataFactory
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $orderItemUnitCreditMemoUnitGenerator,
            $shipmentCreditMemoUnitGenerator,
            $creditMemoNumberGenerator,
            $currentDateTimeProvider,
            $creditMemoIdentifierGenerator,
            $creditMemoFactory,
            $customerBillingDataFactory,
            $shopBillingDataFactory
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
        ShopBillingDataInterface $shopBillingData,
        AddressInterface $customerBillingAddress,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        CreditMemoIdentifierGeneratorInterface $creditMemoIdentifierGenerator,
        CreditMemoFactoryInterface $creditMemoFactory,
        CustomerBillingDataFactoryInterface $customerBillingDataFactory,
        ShopBillingDataFactoryInterface $shopBillingDataFactory,
        CustomerBillingData $customerBillingData,
        ShopBillingData $refundShopBillingData,
        CreditMemoInterface $creditMemo
    ): void {
        $dateTime = new \DateTime();

        $firstUnitRefund = new OrderItemUnitRefund(1, 500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 500);
        $shipmentRefund = new ShipmentRefund(3, 400);

        $orderRepository->findOneByNumber('000666')->willReturn($order);
        $order->getCurrencyCode()->willReturn('GBP');
        $order->getLocaleCode()->willReturn('en_US');

        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB-US');
        $channel->getName()->willReturn('United States');
        $channel->getColor()->willReturn('Linen');

        $channel->getShopBillingData()->willReturn($shopBillingData);
        $order->getBillingAddress()->willReturn($customerBillingAddress);

        $firstCreditMemoUnit = new CreditMemoUnit('Portal gun', 500, 50);
        $orderItemUnitCreditMemoUnitGenerator->generate(1, 500)->willReturn($firstCreditMemoUnit);

        $secondCreditMemoUnit = new CreditMemoUnit('Broken Leg Serum', 500, 50);
        $orderItemUnitCreditMemoUnitGenerator->generate(3, 500)->willReturn($secondCreditMemoUnit);

        $shipmentCreditMemoUnit = new CreditMemoUnit('Galaxy post', 400, 0);
        $shipmentCreditMemoUnitGenerator->generate(3, 400)->willReturn($shipmentCreditMemoUnit);

        $creditMemoNumberGenerator->generate()->willReturn('2018/07/00001111');

        $currentDateTimeProvider->now()->willReturn($dateTime);

        $creditMemoIdentifierGenerator->generate()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $customerBillingDataFactory->createForOrder($order)->willReturn($customerBillingData);
        $shopBillingDataFactory->createForChannelShopBillingData($shopBillingData)->willReturn($refundShopBillingData);

        $creditMemoFactory->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2018/07/00001111',
            '000666',
            1400,
            'GBP',
            'en_US',
            new CreditMemoChannel('WEB-US', 'United States', 'Linen'),
            [
                $firstCreditMemoUnit->serialize(),
                $secondCreditMemoUnit->serialize(),
                $shipmentCreditMemoUnit->serialize(),
            ],
            'Comment',
            $dateTime,
            $customerBillingData,
            $refundShopBillingData
        )->willReturn($creditMemo)->shouldBeCalled();

        $this->generate('000666', 1400, [$firstUnitRefund, $secondUnitRefund], [$shipmentRefund], 'Comment')
            ->shouldReturn($creditMemo->getWrappedObject());
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

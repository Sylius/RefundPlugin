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

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Entity\TaxItemInterface;
use Sylius\RefundPlugin\Factory\CreditMemoFactoryInterface;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactoryInterface;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Generator\TaxItemsGeneratorInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class CreditMemoGeneratorSpec extends ObjectBehavior
{
    function let(
        LineItemsConverterInterface $lineItemsConverter,
        TaxItemsGeneratorInterface $taxItemsGenerator,
        CreditMemoFactoryInterface $creditMemoFactory,
        CustomerBillingDataFactoryInterface $customerBillingDataFactory,
        ShopBillingDataFactoryInterface $shopBillingDataFactory,
    ): void {
        $this->beConstructedWith(
            $lineItemsConverter,
            $taxItemsGenerator,
            $creditMemoFactory,
            $customerBillingDataFactory,
            $shopBillingDataFactory,
        );
    }

    function it_implements_credit_memo_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoGeneratorInterface::class);
    }

    function it_generates_credit_memo_basing_on_event_data(
        LineItemsConverterInterface $lineItemsConverter,
        TaxItemsGeneratorInterface $taxItemsGenerator,
        CreditMemoFactoryInterface $creditMemoFactory,
        CustomerBillingDataFactoryInterface $customerBillingDataFactory,
        CreditMemoInterface $creditMemo,
        CustomerBillingDataInterface $customerBillingData,
        OrderInterface $order,
        ChannelInterface $channel,
        ShopBillingDataInterface $shopBillingData,
        AddressInterface $customerBillingAddress,
        LineItemInterface $firstLineItem,
        LineItemInterface $secondLineItem,
        TaxItemInterface $taxItem,
        ShopBillingDataFactoryInterface $shopBillingDataFactory,
        ShopBillingData $shopBillingDataFromFactory,
    ): void {
        $firstUnitRefund = new OrderItemUnitRefund(1, 500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 500);
        $shipmentRefund = new ShipmentRefund(3, 400);

        $order->getChannel()->willReturn($channel);
        $channel->getShopBillingData()->willReturn($shopBillingData);
        $shopBillingData->getCompany()->willReturn('Needful Things');
        $shopBillingData->getTaxId()->willReturn('000222');
        $shopBillingData->getCountryCode()->willReturn('US');
        $shopBillingData->getStreet()->willReturn('Main St. 123');
        $shopBillingData->getCity()->willReturn('New York');
        $shopBillingData->getPostcode()->willReturn('90222');

        $order->getBillingAddress()->willReturn($customerBillingAddress);
        $customerBillingAddress->getFirstName()->willReturn('Rick');
        $customerBillingAddress->getLastName()->willReturn('Sanchez');
        $customerBillingAddress->getPostcode()->willReturn('000333');
        $customerBillingAddress->getCountryCode()->willReturn('US');
        $customerBillingAddress->getStreet()->willReturn('Universe St. 444');
        $customerBillingAddress->getCity()->willReturn('Los Angeles');
        $customerBillingAddress->getCompany()->willReturn('Curse Purge Plus!');
        $customerBillingAddress->getProvinceName()->willReturn(null);
        $customerBillingAddress->getProvinceCode()->willReturn(null);

        $lineItemsConverter
            ->convert([$firstUnitRefund, $secondUnitRefund, $shipmentRefund])
            ->willReturn([$firstLineItem, $secondLineItem])
        ;

        $taxItemsGenerator->generate([$firstLineItem, $secondLineItem])->willReturn([$taxItem]);
        $customerBillingDataFactory->createWithAddress($customerBillingAddress)->willReturn($customerBillingData);

        $shopBillingDataFactory->createWithData(
            'Needful Things',
            '000222',
            'US',
            'Main St. 123',
            'New York',
            '90222',
        )->willReturn($shopBillingDataFromFactory);

        $creditMemoFactory
            ->createWithData(
                $order,
                1400,
                [$firstLineItem, $secondLineItem],
                [$taxItem],
                'Comment',
                $customerBillingData,
                $shopBillingDataFromFactory,
            )
            ->willReturn($creditMemo)
        ;

        $this
            ->generate($order, 1400, [$firstUnitRefund, $secondUnitRefund, $shipmentRefund], 'Comment')
            ->shouldReturn($creditMemo)
        ;
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_generates_credit_memo_basing_on_event_data_with_deprecations(
        LineItemsConverterInterface $lineItemsConverter,
        LineItemsConverterInterface $shipmentLineItemsConverter,
        TaxItemsGeneratorInterface $taxItemsGenerator,
        CreditMemoFactoryInterface $creditMemoFactory,
        CustomerBillingDataFactoryInterface $customerBillingDataFactory,
        CreditMemoInterface $creditMemo,
        CustomerBillingDataInterface $customerBillingData,
        OrderInterface $order,
        ChannelInterface $channel,
        ShopBillingDataInterface $shopBillingData,
        AddressInterface $customerBillingAddress,
        LineItemInterface $firstLineItem,
        LineItemInterface $secondLineItem,
        TaxItemInterface $taxItem,
        ShopBillingDataFactoryInterface $shopBillingDataFactory,
        ShopBillingData $shopBillingDataFromFactory,
    ): void {
        $this->beConstructedWith(
            $lineItemsConverter,
            $shipmentLineItemsConverter,
            $taxItemsGenerator,
            $creditMemoFactory,
            $customerBillingDataFactory,
            $shopBillingDataFactory,
        );

        $firstUnitRefund = new OrderItemUnitRefund(1, 500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 500);
        $shipmentRefund = new ShipmentRefund(3, 400);

        $order->getChannel()->willReturn($channel);
        $channel->getShopBillingData()->willReturn($shopBillingData);
        $shopBillingData->getCompany()->willReturn('Needful Things');
        $shopBillingData->getTaxId()->willReturn('000222');
        $shopBillingData->getCountryCode()->willReturn('US');
        $shopBillingData->getStreet()->willReturn('Main St. 123');
        $shopBillingData->getCity()->willReturn('New York');
        $shopBillingData->getPostcode()->willReturn('90222');

        $order->getBillingAddress()->willReturn($customerBillingAddress);
        $customerBillingAddress->getFirstName()->willReturn('Rick');
        $customerBillingAddress->getLastName()->willReturn('Sanchez');
        $customerBillingAddress->getPostcode()->willReturn('000333');
        $customerBillingAddress->getCountryCode()->willReturn('US');
        $customerBillingAddress->getStreet()->willReturn('Universe St. 444');
        $customerBillingAddress->getCity()->willReturn('Los Angeles');
        $customerBillingAddress->getCompany()->willReturn('Curse Purge Plus!');
        $customerBillingAddress->getProvinceName()->willReturn(null);
        $customerBillingAddress->getProvinceCode()->willReturn(null);

        $lineItemsConverter->convert([$firstUnitRefund, $secondUnitRefund])->willReturn([$firstLineItem]);
        $shipmentLineItemsConverter->convert([$shipmentRefund])->willReturn([$secondLineItem]);

        $taxItemsGenerator->generate([$firstLineItem, $secondLineItem])->willReturn([$taxItem]);
        $customerBillingDataFactory->createWithAddress($customerBillingAddress)->willReturn($customerBillingData);

        $shopBillingDataFactory->createWithData(
            'Needful Things',
            '000222',
            'US',
            'Main St. 123',
            'New York',
            '90222',
        )->willReturn($shopBillingDataFromFactory);

        $creditMemoFactory
            ->createWithData(
                $order,
                1400,
                [$firstLineItem, $secondLineItem],
                [$taxItem],
                'Comment',
                $customerBillingData,
                $shopBillingDataFromFactory,
            )
            ->willReturn($creditMemo)
        ;

        $this->generate($order, 1400, [$firstUnitRefund, $secondUnitRefund], [$shipmentRefund], 'Comment')->shouldReturn($creditMemo);
    }
}

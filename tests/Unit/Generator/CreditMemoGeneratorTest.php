<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Tests\Unit\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
use Sylius\RefundPlugin\Generator\CreditMemoGenerator;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Generator\TaxItemsGeneratorInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class CreditMemoGeneratorTest extends TestCase
{
    private LineItemsConverterInterface&MockObject $lineItemsConverter;

    private TaxItemsGeneratorInterface&MockObject $taxItemsGenerator;

    private CreditMemoFactoryInterface&MockObject $creditMemoFactory;

    private CustomerBillingDataFactoryInterface&MockObject $customerBillingDataFactory;

    private ShopBillingDataFactoryInterface&MockObject $shopBillingDataFactory;

    private CreditMemoGenerator $creditMemoGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lineItemsConverter = $this->createMock(LineItemsConverterInterface::class);
        $this->taxItemsGenerator = $this->createMock(TaxItemsGeneratorInterface::class);
        $this->creditMemoFactory = $this->createMock(CreditMemoFactoryInterface::class);
        $this->customerBillingDataFactory = $this->createMock(CustomerBillingDataFactoryInterface::class);
        $this->shopBillingDataFactory = $this->createMock(ShopBillingDataFactoryInterface::class);

        $this->creditMemoGenerator = new CreditMemoGenerator(
            $this->lineItemsConverter,
            $this->taxItemsGenerator,
            $this->creditMemoFactory,
            $this->customerBillingDataFactory,
            $this->shopBillingDataFactory,
        );
    }

    public function testItImplementsCreditMemoGeneratorInterface(): void
    {
        self::assertInstanceOf(CreditMemoGeneratorInterface::class, $this->creditMemoGenerator);
    }

    public function testItGeneratesCreditMemoBasingOnEventData(): void
    {
        $firstUnitRefund = new OrderItemUnitRefund(1, 500);
        $secondUnitRefund = new OrderItemUnitRefund(3, 500);
        $shipmentRefund = new ShipmentRefund(3, 400);

        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $customerBillingData = $this->createMock(CustomerBillingDataInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);
        $customerBillingAddress = $this->createMock(AddressInterface::class);
        $firstLineItem = $this->createMock(LineItemInterface::class);
        $secondLineItem = $this->createMock(LineItemInterface::class);
        $taxItem = $this->createMock(TaxItemInterface::class);
        $shopBillingDataFromFactory = $this->createMock(ShopBillingData::class);

        $order->expects(self::once())
            ->method('getChannel')
            ->willReturn($channel);

        $channel->expects(self::once())
            ->method('getShopBillingData')
            ->willReturn($shopBillingData);

        $shopBillingData->expects(self::once())
            ->method('getCompany')
            ->willReturn('Needful Things');

        $shopBillingData->expects(self::once())
            ->method('getTaxId')
            ->willReturn('000222');

        $shopBillingData->expects(self::once())
            ->method('getCountryCode')
            ->willReturn('US');

        $shopBillingData->expects($this->exactly(2))
            ->method('getStreet')
            ->willReturn('Main St. 123');

        $shopBillingData->expects(self::once())
            ->method('getCity')
            ->willReturn('New York');

        $shopBillingData->expects(self::once())
            ->method('getPostcode')
            ->willReturn('90222');

        $order->expects(self::once())
            ->method('getBillingAddress')
            ->willReturn($customerBillingAddress);

        $customerBillingAddress->expects($this->never())
            ->method('getFirstName');

        $customerBillingAddress->expects($this->never())
            ->method('getLastName');

        $customerBillingAddress->expects($this->never())
            ->method('getPostcode');

        $customerBillingAddress->expects($this->never())
            ->method('getCountryCode');

        $customerBillingAddress->expects($this->never())
            ->method('getStreet');

        $customerBillingAddress->expects($this->never())
            ->method('getCity');

        $customerBillingAddress->expects($this->never())
            ->method('getCompany');

        $customerBillingAddress->expects($this->never())
            ->method('getProvinceName');

        $customerBillingAddress->expects($this->never())
            ->method('getProvinceCode');

        $this->lineItemsConverter->expects(self::once())
            ->method('convert')
            ->with([$firstUnitRefund, $secondUnitRefund, $shipmentRefund])
            ->willReturn([$firstLineItem, $secondLineItem]);

        $this->taxItemsGenerator->expects(self::once())
            ->method('generate')
            ->with([$firstLineItem, $secondLineItem])
            ->willReturn([$taxItem]);

        $this->customerBillingDataFactory->expects(self::once())
            ->method('createWithAddress')
            ->with($customerBillingAddress)
            ->willReturn($customerBillingData);

        $this->shopBillingDataFactory->expects(self::once())
            ->method('createWithData')
            ->with(
                'Needful Things',
                '000222',
                'US',
                'Main St. 123',
                'New York',
                '90222',
            )
            ->willReturn($shopBillingDataFromFactory);

        $this->creditMemoFactory->expects(self::once())
            ->method('createWithData')
            ->with(
                $order,
                1400,
                [$firstLineItem, $secondLineItem],
                [$taxItem],
                'Comment',
                $customerBillingData,
                $shopBillingDataFromFactory,
            )
            ->willReturn($creditMemo);

        $result = $this->creditMemoGenerator->generate(
            $order,
            1400,
            [$firstUnitRefund, $secondUnitRefund, $shipmentRefund],
            'Comment',
        );

        self::assertSame($creditMemo, $result);
    }
}

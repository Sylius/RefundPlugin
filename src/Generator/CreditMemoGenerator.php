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

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface as ChannelShopBillingData;
use Sylius\RefundPlugin\Converter\LineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Factory\CreditMemoFactoryInterface;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactoryInterface;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;
use Webmozart\Assert\Assert;

final class CreditMemoGenerator implements CreditMemoGeneratorInterface
{
    private LineItemsConverterInterface $lineItemsConverter;

    private TaxItemsGeneratorInterface $taxItemsGenerator;

    private CreditMemoFactoryInterface $creditMemoFactory;

    private CustomerBillingDataFactoryInterface $customerBillingDataFactory;

    private ShopBillingDataFactoryInterface $shopBillingDataFactory;

    public function __construct(
        LineItemsConverterInterface $lineItemsConverter,
        TaxItemsGeneratorInterface $taxItemsGenerator,
        CreditMemoFactoryInterface $creditMemoFactory,
        CustomerBillingDataFactoryInterface $customerBillingDataFactory,
        ShopBillingDataFactoryInterface $shopBillingDataFactory,
    ) {
        $this->lineItemsConverter = $lineItemsConverter;
        $this->taxItemsGenerator = $taxItemsGenerator;
        $this->creditMemoFactory = $creditMemoFactory;
        $this->customerBillingDataFactory = $customerBillingDataFactory;
        $this->shopBillingDataFactory = $shopBillingDataFactory;
    }

    public function generate(
        OrderInterface $order,
        int $total,
        array $units,
        string $comment,
    ): CreditMemoInterface {
        /** @var ChannelInterface|null $channel */
        $channel = $order->getChannel();
        Assert::notNull($channel);

        /** @var AddressInterface|null $billingAddress */
        $billingAddress = $order->getBillingAddress();
        Assert::notNull($billingAddress);

        $lineItems = $this->lineItemsConverter->convert($units);

        return $this->creditMemoFactory->createWithData(
            $order,
            $total,
            $lineItems,
            $this->taxItemsGenerator->generate($lineItems),
            $comment,
            $this->getFromAddress($billingAddress),
            $this->getToAddress($channel->getShopBillingData()),
        );
    }

    private function getFromAddress(AddressInterface $address): CustomerBillingDataInterface
    {
        return $this->customerBillingDataFactory->createWithAddress($address);
    }

    private function getToAddress(?ChannelShopBillingData $channelShopBillingData): ?ShopBillingDataInterface
    {
        if (
            $channelShopBillingData === null ||
            ($channelShopBillingData->getStreet() === null && $channelShopBillingData->getCompany() === null)
        ) {
            return null;
        }

        return $this->shopBillingDataFactory->createWithData(
            $channelShopBillingData->getCompany(),
            $channelShopBillingData->getTaxId(),
            $channelShopBillingData->getCountryCode(),
            $channelShopBillingData->getStreet(),
            $channelShopBillingData->getCity(),
            $channelShopBillingData->getPostcode(),
        );
    }
}

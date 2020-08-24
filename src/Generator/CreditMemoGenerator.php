<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface as ChannelShopBillingData;
use Sylius\RefundPlugin\Converter\LineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;
use Webmozart\Assert\Assert;

final class CreditMemoGenerator implements CreditMemoGeneratorInterface
{
    /** @var LineItemsConverterInterface */
    private $lineItemsConverter;

    /** @var LineItemsConverterInterface */
    private $shipmentLineItemsConverter;

    /** @var TaxItemsGeneratorInterface */
    private $taxItemsGenerator;

    /** @var NumberGenerator */
    private $creditMemoNumberGenerator;

    /** @var CurrentDateTimeImmutableProviderInterface */
    private $currentDateTimeImmutableProvider;

    /** @var CreditMemoIdentifierGeneratorInterface */
    private $uuidCreditMemoIdentifierGenerator;

    public function __construct(
        LineItemsConverterInterface $lineItemsConverter,
        LineItemsConverterInterface $shipmentLineItemsConverter,
        TaxItemsGeneratorInterface $taxItemsGenerator,
        NumberGenerator $creditMemoNumberGenerator,
        CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider,
        CreditMemoIdentifierGeneratorInterface $uuidCreditMemoIdentifierGenerator
    ) {
        $this->lineItemsConverter = $lineItemsConverter;
        $this->shipmentLineItemsConverter = $shipmentLineItemsConverter;
        $this->taxItemsGenerator = $taxItemsGenerator;
        $this->creditMemoNumberGenerator = $creditMemoNumberGenerator;
        $this->currentDateTimeImmutableProvider = $currentDateTimeImmutableProvider;
        $this->uuidCreditMemoIdentifierGenerator = $uuidCreditMemoIdentifierGenerator;
    }

    public function generate(
        OrderInterface $order,
        int $total,
        array $units,
        array $shipments,
        string $comment
    ): CreditMemoInterface {
        Assert::allIsInstanceOf($units, OrderItemUnitRefund::class);
        Assert::allIsInstanceOf($shipments, ShipmentRefund::class);

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        Assert::notNull($channel);

        $lineItems = array_merge(
            $this->lineItemsConverter->convert($units),
            $this->shipmentLineItemsConverter->convert($shipments)
        );

        $billingAddress = $order->getBillingAddress();

        Assert::notNull($billingAddress);

        return new CreditMemo(
            $this->uuidCreditMemoIdentifierGenerator->generate(),
            $this->creditMemoNumberGenerator->generate(),
            $order,
            $total,
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            $channel,
            $lineItems,
            $this->taxItemsGenerator->generate($lineItems),
            $comment,
            $this->currentDateTimeImmutableProvider->now(),
            $this->getFromAddress($billingAddress),
            $this->getToAddress($channel->getShopBillingData())
        );
    }

    private function getFromAddress(AddressInterface $address): CustomerBillingData
    {
        return new CustomerBillingData(
            $address->getFirstName(),
            $address->getLastName(),
            $address->getStreet(),
            $address->getPostcode(),
            $address->getCountryCode(),
            $address->getCity(),
            $address->getCompany(),
            $address->getProvinceName(),
            $address->getProvinceCode()
        );
    }

    private function getToAddress(?ChannelShopBillingData $channelShopBillingData): ?ShopBillingData
    {
        if (
            $channelShopBillingData === null ||
            ($channelShopBillingData->getStreet() === null && $channelShopBillingData->getCompany() === null)
        ) {
            return null;
        }

        return new ShopBillingData(
            $channelShopBillingData->getCompany(),
            $channelShopBillingData->getTaxId(),
            $channelShopBillingData->getCountryCode(),
            $channelShopBillingData->getStreet(),
            $channelShopBillingData->getCity(),
            $channelShopBillingData->getPostcode()
        );
    }
}

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
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Converter\LineItemsConverterInterface as LegacyLineItemsConverterInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Factory\CreditMemoFactoryInterface;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactoryInterface;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactoryInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Webmozart\Assert\Assert;

final class CreditMemoGenerator implements CreditMemoGeneratorInterface
{
    private LineItemsConverterInterface $shipmentLineItemsConverter;

    public function __construct(
        private LineItemsConverterInterface|LegacyLineItemsConverterInterface $lineItemsConverter,
        private TaxItemsGeneratorInterface|LineItemsConverterInterface $taxItemsGenerator,
        private CreditMemoFactoryInterface|TaxItemsGeneratorInterface $creditMemoFactory,
        private CustomerBillingDataFactoryInterface|CreditMemoFactoryInterface $customerBillingDataFactory,
        private ShopBillingDataFactoryInterface|CustomerBillingDataFactoryInterface $shopBillingDataFactory,
    ) {
        $args = func_get_args();

        if ($taxItemsGenerator instanceof LineItemsConverterInterface) {
            if (!isset($args[5])) {
                throw new \InvalidArgumentException('The 6th argument must be present.');
            }

            $this->shipmentLineItemsConverter = $taxItemsGenerator;
            /** @phpstan-ignore-next-line */
            $this->taxItemsGenerator = $creditMemoFactory;
            /** @phpstan-ignore-next-line */
            $this->creditMemoFactory = $customerBillingDataFactory;
            /** @phpstan-ignore-next-line */
            $this->customerBillingDataFactory = $shopBillingDataFactory;
            $this->shopBillingDataFactory = $args[5];

            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Passing "%s" as a 2nd argument of "%s" constructor is deprecated and will be removed in 2.0.', LineItemsConverterInterface::class, self::class));
        }
    }

    public function generate(
        OrderInterface $order,
        int $total,
        array $units,
        string|array $comment,
    ): CreditMemoInterface {
        $args = func_get_args();
        $shipments = null;

        if (is_array($comment)) {
            if (!isset($args[4]) || !is_string($args[4])) {
                throw new \InvalidArgumentException('The 5th argument must be present.');
            }

            $shipments = $comment;
            $comment = $args[4];

            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Passing an array as a 4th argument of "%s::generate" method is deprecated and will be removed in 2.0.', self::class));

            Assert::allIsInstanceOf($units, OrderItemUnitRefund::class);
            Assert::allIsInstanceOf($shipments, ShipmentRefund::class);
        }

        Assert::isInstanceOf($this->creditMemoFactory, CreditMemoFactoryInterface::class);
        Assert::isInstanceOf($this->taxItemsGenerator, TaxItemsGeneratorInterface::class);

        /** @var ChannelInterface|null $channel */
        $channel = $order->getChannel();
        Assert::notNull($channel);

        /** @var AddressInterface|null $billingAddress */
        $billingAddress = $order->getBillingAddress();
        Assert::notNull($billingAddress);

        if ($shipments !== null) {
            $lineItems = array_merge(
                $this->lineItemsConverter->convert($units),
                $this->shipmentLineItemsConverter->convert($shipments),
            );
        } else {
            $lineItems = $this->lineItemsConverter->convert($units);
        }

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
        Assert::isInstanceOf($this->customerBillingDataFactory, CustomerBillingDataFactoryInterface::class);

        return $this->customerBillingDataFactory->createWithAddress($address);
    }

    private function getToAddress(?ChannelShopBillingData $channelShopBillingData): ?ShopBillingDataInterface
    {
        Assert::isInstanceOf($this->shopBillingDataFactory, ShopBillingDataFactoryInterface::class);

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

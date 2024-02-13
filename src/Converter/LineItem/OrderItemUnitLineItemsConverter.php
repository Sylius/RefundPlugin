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

namespace Sylius\RefundPlugin\Converter\LineItem;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Factory\LineItemFactoryInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;
use Webmozart\Assert\Assert;

final class OrderItemUnitLineItemsConverter implements LineItemsConverterUnitRefundAwareInterface
{
    public function __construct(
        private RepositoryInterface $orderItemUnitRepository,
        private TaxRateProviderInterface $taxRateProvider,
        private ?LineItemFactoryInterface $lineItemFactory = null,
    ) {
        if (null === $this->lineItemFactory) {
            trigger_deprecation(
                'sylius/refund-plugin',
                '1.5',
                'Not passing a line item factory to "%s" is deprecated and will be removed in 2.0.',
                self::class,
            );
        }
    }

    public function convert(array $units): array
    {
        Assert::allIsInstanceOf($units, $this->getUnitRefundClass());

        $lineItems = [];

        /** @var OrderItemUnitRefund $unit */
        foreach ($units as $unit) {
            $lineItems = $this->addLineItem($this->convertUnitRefundToLineItem($unit), $lineItems);
        }

        return $lineItems;
    }

    public function getUnitRefundClass(): string
    {
        return OrderItemUnitRefund::class;
    }

    private function convertUnitRefundToLineItem(OrderItemUnitRefund $unitRefund): LineItemInterface
    {
        /** @var OrderItemUnitInterface|null $orderItemUnit */
        $orderItemUnit = $this->orderItemUnitRepository->find($unitRefund->id());
        Assert::notNull($orderItemUnit);
        Assert::lessThanEq($unitRefund->total(), $orderItemUnit->getTotal());

        /** @var OrderItemInterface $orderItem */
        $orderItem = $orderItemUnit->getOrderItem();

        $grossValue = $unitRefund->total();
        $taxAmount = (int) ($grossValue * $orderItemUnit->getTaxTotal() / $orderItemUnit->getTotal());
        $netValue = $grossValue - $taxAmount;

        /** @var string|null $productName */
        $productName = $orderItem->getProductName();
        Assert::notNull($productName);

        if (null === $this->lineItemFactory) {
            return new LineItem(
                $productName,
                1,
                $netValue,
                $grossValue,
                $netValue,
                $grossValue,
                $taxAmount,
                $this->taxRateProvider->provide($orderItemUnit),
            );
        }

        return $this->lineItemFactory->createWithData(
            name: $productName,
            quantity: 1,
            unitNetPrice: $netValue,
            unitGrossPrice: $grossValue,
            netValue: $netValue,
            grossValue: $grossValue,
            taxAmount: $taxAmount,
            taxRate: $this->taxRateProvider->provide($orderItemUnit),
        );
    }

    /**
     * @param LineItemInterface[] $lineItems
     *
     * @return LineItemInterface[]
     */
    private function addLineItem(LineItemInterface $newLineItem, array $lineItems): array
    {
        foreach ($lineItems as $lineItem) {
            if ($lineItem->compare($newLineItem)) {
                $lineItem->merge($newLineItem);

                return $lineItems;
            }
        }

        $lineItems[] = $newLineItem;

        return $lineItems;
    }
}

class_alias(OrderItemUnitLineItemsConverter::class, \Sylius\RefundPlugin\Converter\OrderItemUnitLineItemsConverter::class);

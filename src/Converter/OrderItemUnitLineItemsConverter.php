<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;
use Webmozart\Assert\Assert;

final class OrderItemUnitLineItemsConverter implements LineItemsConverterInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    /** @var TaxRateProviderInterface */
    private $taxRateProvider;

    public function __construct(RepositoryInterface $orderItemUnitRepository, TaxRateProviderInterface $taxRateProvider)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
        $this->taxRateProvider = $taxRateProvider;
    }

    public function convert(array $units): array
    {
        Assert::allIsInstanceOf($units, OrderItemUnitRefund::class);

        $lineItems = [];

        /** @var OrderItemUnitRefund $unitRefund */
        foreach ($units as $unitRefund) {
            $lineItems = $this->addLineItem($this->convertUnitRefundToLineItem($unitRefund), $lineItems);
        }

        return $lineItems;
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

        return new LineItem(
            $orderItem->getProductName(),
            1,
            $netValue,
            $grossValue,
            $netValue,
            $grossValue,
            $taxAmount,
            $this->taxRateProvider->provide($orderItemUnit)
        );
    }

    /**
     * @param LineItemInterface[] $lineItems
     *
     * @return LineItemInterface[]
     */
    private function addLineItem(LineItemInterface $newLineItem, array $lineItems): array
    {
        /** @var LineItemInterface $lineItem */
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

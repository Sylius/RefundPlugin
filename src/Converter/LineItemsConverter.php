<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

final class LineItemsConverter implements LineItemsConverterInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    public function __construct(RepositoryInterface $orderItemUnitRepository)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
    }

    public function convert(array $units): Collection
    {
        $lineItems = new ArrayCollection();

        /** @var UnitRefundInterface $unitRefund */
        foreach ($units as $unitRefund) {
            /** @var OrderItemUnitInterface $orderItemUnit */
            $orderItemUnit = $this->orderItemUnitRepository->find($unitRefund->id());
            Assert::notNull($orderItemUnit);
            Assert::lessThanEq($unitRefund->total(), $orderItemUnit->getTotal());

            /** @var OrderItemInterface $orderItem */
            $orderItem = $orderItemUnit->getOrderItem();

            $grossValue = $unitRefund->total();
            $taxAmount = (int) ($grossValue * $orderItemUnit->getTaxTotal() / $orderItemUnit->getTotal());
            $netValue = $grossValue - $taxAmount;

            /** @var Collection|AdjustmentInterface[] $taxAdjustments */
            $taxAdjustments = $orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

            $lineItems = $this->addLineItem(new LineItem(
                $orderItem->getProductName(),
                1,
                $netValue,
                $grossValue,
                $netValue,
                $grossValue,
                $taxAmount,
                $this->getTaxRate($taxAdjustments)
            ), $lineItems);
        }

        return $lineItems;
    }

    /**
     * @param Collection|LineItemInterface[] $lineItems
     *
     * @return Collection|LineItemInterface[]
     */
    private function addLineItem(LineItemInterface $newLineItem, Collection $lineItems): Collection
    {
        /** @var LineItemInterface $lineItem */
        foreach ($lineItems as $lineItem) {
            if (
                $lineItem->name() === $newLineItem->name() &&
                $lineItem->unitNetPrice() === $newLineItem->unitNetPrice() &&
                $lineItem->unitGrossPrice() === $newLineItem->unitGrossPrice() &&
                $lineItem->taxRate() === $newLineItem->taxRate()
            ) {
                $lineItem->merge($newLineItem);

                return $lineItems;
            }
        }

        $lineItems->add($newLineItem);

        return $lineItems;
    }

    /**
     * @param Collection|AdjustmentInterface[] $taxAdjustments
     */
    private function getTaxRate(Collection $taxAdjustments): ?string
    {
        if ($taxAdjustments->isEmpty()) {
            return null;
        }

        $label = $taxAdjustments->first()->getLabel();

        if (preg_match('#\((.*?)\)#', $label, $matches)) {
            return end($matches);
        }

        return $label;
    }
}

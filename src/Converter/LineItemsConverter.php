<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

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

    public function convert(array $units): array
    {
        $lineItems = [];

        /** @var UnitRefundInterface $unitRefund */
        foreach ($units as $unitRefund) {
            $lineItems = $this->addLineItem($this->convertUnitRefundToLineItem($unitRefund), $lineItems);
        }

        return $lineItems;
    }

    private function convertUnitRefundToLineItem(UnitRefundInterface $unitRefund): LineItemInterface
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

        /** @var Collection|AdjustmentInterface[] $taxAdjustments */
        $taxAdjustments = $orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        return new LineItem(
            $orderItem->getProductName(),
            1,
            $netValue,
            $grossValue,
            $netValue,
            $grossValue,
            $taxAmount,
            $this->getTaxRate($taxAdjustments)
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

    /**
     * @param Collection|AdjustmentInterface[] $taxAdjustments
     */
    private function getTaxRate(Collection $taxAdjustments): ?string
    {
        if ($taxAdjustments->isEmpty()) {
            return null;
        }

        $label = $taxAdjustments->first()->getLabel();

        if (preg_match('/\((.*?)\)/', $label, $matches)) {
            return end($matches); // returns percent tax rate from tax adjustment label
        }

        return $label;
    }
}

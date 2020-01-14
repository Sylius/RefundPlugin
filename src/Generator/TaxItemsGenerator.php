<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\TaxItem;
use Sylius\RefundPlugin\Entity\TaxItemInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class TaxItemsGenerator implements TaxItemsGeneratorInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    public function __construct(RepositoryInterface $orderItemUnitRepository)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
    }

    public function generate(array $units): array
    {
        $temporaryTaxItems = [];

        /** @var UnitRefundInterface $unitRefund */
        foreach ($units as $unitRefund) {
            $temporaryTaxItems = $this->generateTaxItemsForUnit($unitRefund, $temporaryTaxItems);
        }

        return $this->prepareTaxItemsArray($temporaryTaxItems);
    }

    /**
     * @param array<string, int> $temporaryTaxItems
     *
     * @return array<string, int>
     */
    private function generateTaxItemsForUnit(UnitRefundInterface $unitRefund, array $temporaryTaxItems): array
    {
        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $this->orderItemUnitRepository->find($unitRefund->id());

        $taxAdjustments = $orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        foreach ($taxAdjustments as $taxAdjustment) {
            $taxAmount = (int) ($orderItemUnit->getTaxTotal() * ($unitRefund->total() / $orderItemUnit->getTotal()));

            if (isset($temporaryTaxItems[$taxAdjustment->getLabel()])) {
                $temporaryTaxItems[$taxAdjustment->getLabel()] += $taxAmount;

                continue;
            }

            $temporaryTaxItems[$taxAdjustment->getLabel()] = $taxAmount;
        }

        return $temporaryTaxItems;
    }

    /** @return array<TaxItemInterface> */
    private function prepareTaxItemsArray(array $temporaryTaxItems): array
    {
        $taxItems = [];
        foreach ($temporaryTaxItems as $label => $amount) {
            $taxItems[] = new TaxItem($label, $amount);
        }

        return $taxItems;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

final class TaxRateProvider implements TaxRateProviderInterface
{
    public function provide(OrderItemUnitInterface $orderItemUnit): ?string
    {
        /** @var Collection|AdjustmentInterface[] $taxAdjustments */
        $taxAdjustments = $orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

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

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;

final class TaxRateProvider implements TaxRateProviderInterface
{
    public function provide(OrderItemUnitInterface $orderItemUnit): ?string
    {
        /** @var Collection|AdjustmentInterface[] $taxAdjustments */
        $taxAdjustments = $orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        if ($taxAdjustments->isEmpty() || !key_exists('taxRateAmount', $taxAdjustments->first()->getDetails())) {
            return null;
        }

        return $taxAdjustments->first()->getDetails()['taxRateAmount'] * 100 . '%';
    }
}

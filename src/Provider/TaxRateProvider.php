<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Webmozart\Assert\Assert;

final class TaxRateProvider implements TaxRateProviderInterface
{
    public function provide(OrderItemUnitInterface $orderItemUnit): ?string
    {
        /** @var Collection|AdjustmentInterface[] $taxAdjustments */
        $taxAdjustments = $orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        Assert::maxCount($taxAdjustments, 1, 'Every Order Item Unit Should have max 1 tax adjustment');

        if ($taxAdjustments->isEmpty() || !key_exists('taxRateAmount', $taxAdjustments->first()->getDetails())) {
            return null;
        }

        return $taxAdjustments->first()->getDetails()['taxRateAmount'] * 100 . '%';
    }
}

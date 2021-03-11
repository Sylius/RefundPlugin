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

namespace Sylius\RefundPlugin\Provider;

use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Webmozart\Assert\Assert;

final class TaxRateAmountProvider implements TaxRateAmountProviderInterface
{
    public function provide(AdjustmentInterface $adjustment): float
    {
        Assert::keyExists(
            $adjustment->getDetails(),
            'taxRateAmount',
            'There is no tax rate amount in details of this adjustment'
        );

        return $adjustment->getDetails()['taxRateAmount'];
    }
}

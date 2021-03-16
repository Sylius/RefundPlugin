<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Order\Model\AdjustableInterface;

interface TaxRateProviderInterface
{
    public function provide(AdjustableInterface $adjustable): ?string;
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\RefundPlugin\Entity\TaxItemInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

interface TaxItemsGeneratorInterface
{
    /**
     * @param array<UnitRefundInterface> $units
     *
     * @return array<TaxItemInterface>
     */
    public function generate(array $units): array;
}

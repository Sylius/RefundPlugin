<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\TaxItemInterface;

interface TaxItemsGeneratorInterface
{
    /**
     * @param LineItemInterface[] $lineItems
     *
     * @return array<TaxItemInterface>
     */
    public function generate(array $lineItems): array;
}

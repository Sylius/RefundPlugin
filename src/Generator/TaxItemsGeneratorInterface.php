<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Doctrine\Common\Collections\Collection;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\TaxItemInterface;

interface TaxItemsGeneratorInterface
{
    /**
     * @param Collection|LineItemInterface[] $lineItems
     *
     * @return array<TaxItemInterface>
     */
    public function generate(Collection $lineItems): array;
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

interface LineItemsConverterInterface
{
    /**
     * @param UnitRefundInterface[] $units
     *
     * @return LineItemInterface[]
     */
    public function convert(array $units): array;
}

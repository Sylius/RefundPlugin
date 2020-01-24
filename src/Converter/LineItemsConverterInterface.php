<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Doctrine\Common\Collections\Collection;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

interface LineItemsConverterInterface
{
    /**
     * @param array<UnitRefundInterface> $units
     *
     * @return Collection|LineItemInterface[]
     */
    public function convert(array $units): Collection;
}

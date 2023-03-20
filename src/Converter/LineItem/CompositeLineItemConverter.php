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

namespace Sylius\RefundPlugin\Converter\LineItem;

use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
use Webmozart\Assert\Assert;

final class CompositeLineItemConverter implements LineItemsConverterInterface
{
    public function __construct(
        /** @param LineItemsConverterUnitRefundAwareInterface[] $lineItemsConverters */
        private iterable $lineItemsConverters,
        private UnitRefundFilterInterface $unitRefundFilter,
    ) {
    }

    public function convert(array $units): array
    {
        $lineItems = [];

        foreach ($this->lineItemsConverters as $lineItemsConverter) {
            Assert::isInstanceOf($lineItemsConverter, LineItemsConverterUnitRefundAwareInterface::class);

            $filteredUnits = $this->unitRefundFilter->filterUnitRefunds(
                $units,
                $lineItemsConverter->getUnitRefundClass(),
            );

            $lineItems = array_merge($lineItems, $lineItemsConverter->convert($filteredUnits));
        }

        return $lineItems;
    }
}

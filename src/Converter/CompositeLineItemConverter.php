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

namespace Sylius\RefundPlugin\Converter;

final class CompositeLineItemConverter implements LineItemsConverterInterface
{
    /** @param LineItemsConverterInterface[] $lineItemsConverters */
    public function __construct(private iterable $lineItemsConverters)
    {
    }

    public function convert(array $units): array
    {
        $lineItems = [];

        foreach ($this->lineItemsConverters as $lineItemsConverter) {
            $lineItems = array_merge($lineItems, $lineItemsConverter->convert($units));
        }

        return $lineItems;
    }
}

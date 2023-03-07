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

use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

final class CompositeLineItemConverter implements LineItemsConverterInterface
{
    /** @param LineItemsConverterUnitRefundAwareInterface[] $lineItemsConverters */
    public function __construct(private iterable $lineItemsConverters)
    {
    }

    public function convert(array $units): array
    {
        $lineItems = [];

        foreach ($this->lineItemsConverters as $lineItemsConverter) {
            Assert::isInstanceOf($lineItemsConverter, LineItemsConverterUnitRefundAwareInterface::class);

            $lineItems = array_merge($lineItems, $lineItemsConverter->convert($this->filterUnits($units, $lineItemsConverter->getUnitRefundClass())));
        }

        return $lineItems;
    }

    /**
     * @template T of UnitRefundInterface
     *
     * @param class-string<T> $unitRefundClass
     *
     * @return T[]
     */
    private function filterUnits(array $units, string $unitRefundClass): array
    {
        return array_values(
            array_filter($units, function (UnitRefundInterface $unitRefund) use ($unitRefundClass): bool {
                return $unitRefund instanceof $unitRefundClass;
            }),
        );
    }
}

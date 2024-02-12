<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\LineItemInterface;
use Webmozart\Assert\Assert;

class LineItemFactory implements LineItemFactoryInterface
{
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createNew(): LineItemInterface
    {
        throw new \InvalidArgumentException('Default creation method is forbidden for this object. Use `createWithData` instead.');
    }

    public function createWithData(
        string $name,
        int $quantity,
        int $unitNetPrice,
        int $unitGrossPrice,
        int $netValue,
        int $grossValue,
        int $taxAmount,
        ?string $taxRate = null,
    ): LineItemInterface {
        $lineItem = new $this->className(
            $name,
            $quantity,
            $unitNetPrice,
            $unitGrossPrice,
            $netValue,
            $grossValue,
            $taxAmount,
            $taxRate,
        );

        Assert::isInstanceOf($lineItem, LineItemInterface::class);

        return $lineItem;
    }
}

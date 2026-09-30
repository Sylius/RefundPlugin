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

namespace Tests\Sylius\RefundPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Behat\Context\Setup\ProductContext as BaseProductContext;

final class ProductContext implements Context
{
    public function __construct(private readonly BaseProductContext $baseProductContext)
    {
    }

    #[Given('the store has a free product :productName')]
    public function theStoreHasAFreeProduct(string $productName): void
    {
        $this->baseProductContext->storeHasAProductPricedAt($productName, 0, null);
    }
}

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

use Sylius\RefundPlugin\Model\UnitRefundInterface;

interface LineItemsConverterUnitRefundAwareInterface extends LineItemsConverterInterface
{
    /**
     * @return class-string<UnitRefundInterface>
     */
    public function getUnitRefundClass(): string;
}

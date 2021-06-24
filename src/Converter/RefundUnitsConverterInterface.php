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

use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

interface RefundUnitsConverterInterface
{
    /**
     * @return UnitRefundInterface[]
     */
    public function convert(array $units, RefundType $refundType, string $unitRefundClass): array;
}

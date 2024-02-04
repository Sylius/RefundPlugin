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

namespace Sylius\RefundPlugin\Converter;

use Sylius\RefundPlugin\Model\RefundTypeInterface;

/**
 * @method array convert(array $units, string|RefundTypeInterface $unitRefundClass)
 */
interface RefundUnitsConverterInterface
{
}

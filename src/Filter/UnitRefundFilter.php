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

namespace Sylius\RefundPlugin\Filter;

use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class UnitRefundFilter implements UnitRefundFilterInterface
{
    public function filterUnitRefunds(array $units, string $unitRefundClass): array
    {
        return array_values(
            array_filter($units, function (UnitRefundInterface $unit) use ($unitRefundClass): bool {
                return $unit instanceof $unitRefundClass;
            }),
        );
    }
}

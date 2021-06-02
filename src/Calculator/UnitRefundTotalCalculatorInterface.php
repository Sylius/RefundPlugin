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

namespace Sylius\RefundPlugin\Calculator;

use Sylius\RefundPlugin\Model\RefundType;

interface UnitRefundTotalCalculatorInterface
{
    public function calculateForUnitWithIdAndType(int $id, RefundType $refundType, ?float $amount = null): int;
}

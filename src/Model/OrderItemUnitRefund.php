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

namespace Sylius\RefundPlugin\Model;

final class OrderItemUnitRefund implements UnitRefundInterface
{
    /** @var int */
    private $unitId;

    /** @var int */
    private $total;

    public function __construct(int $unitId, int $total)
    {
        $this->unitId = $unitId;
        $this->total = $total;
    }

    public function id(): int
    {
        return $this->unitId;
    }

    public function total(): int
    {
        return $this->total;
    }
}

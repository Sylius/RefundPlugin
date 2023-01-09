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

namespace Sylius\RefundPlugin\Command;

use Sylius\RefundPlugin\Model\UnitRefundInterface;

class GenerateCreditMemo
{
    public function __construct(
        private string $orderNumber,
        private int $total,
        /** @var array|UnitRefundInterface[] */
        private array $units,
        private string $comment,
    ) {
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function total(): int
    {
        return $this->total;
    }

    /** @return array|UnitRefundInterface[] */
    public function units(): array
    {
        return $this->units;
    }

    public function comment(): string
    {
        return $this->comment;
    }
}

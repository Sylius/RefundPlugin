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

namespace Sylius\RefundPlugin\Event;

class CreditMemoGenerated
{
    private string $number;

    private string $orderNumber;

    public function __construct(string $number, string $orderNumber)
    {
        $this->number = $number;
        $this->orderNumber = $orderNumber;
    }

    public function number(): string
    {
        return $this->number;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }
}

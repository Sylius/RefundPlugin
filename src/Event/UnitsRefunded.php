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

namespace Sylius\RefundPlugin\Event;

use Sylius\RefundPlugin\Model\UnitRefundInterface;

class UnitsRefunded
{
    public function __construct(
        private string $orderNumber,
        /** @var array|UnitRefundInterface[] */
        private array $units,
        private int $paymentMethodId,
        private int $amount,
        private string $currencyCode,
        private string $comment,
    ) {
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    /** @return array|UnitRefundInterface[] */
    public function units(): array
    {
        return $this->units;
    }

    public function paymentMethodId(): int
    {
        return $this->paymentMethodId;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function comment(): string
    {
        return $this->comment;
    }
}

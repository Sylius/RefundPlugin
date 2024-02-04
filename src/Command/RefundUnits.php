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
use Webmozart\Assert\Assert;

class RefundUnits
{
    public function __construct(
        private string $orderNumber,
        private array $units,
        private int $paymentMethodId,
        private string $comment,
    ) {
        Assert::allIsInstanceOf($units, UnitRefundInterface::class);
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
        Assert::integer($this->paymentMethodId);

        return $this->paymentMethodId;
    }

    public function comment(): string
    {
        Assert::string($this->comment);

        return $this->comment;
    }
}

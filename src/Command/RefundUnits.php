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

use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

class RefundUnits
{
    private string $orderNumber;

    /** @var array|OrderItemUnitRefund[] */
    private array $units;

    /** @var array|ShipmentRefund[] */
    private array $shipments;

    private int $paymentMethodId;

    private string $comment;

    public function __construct(string $orderNumber, array $units, array $shipments, int $paymentMethodId, string $comment)
    {
        Assert::allIsInstanceOf($units, UnitRefundInterface::class);
        Assert::allIsInstanceOf($shipments, UnitRefundInterface::class);

        $this->orderNumber = $orderNumber;
        $this->units = $units;
        $this->shipments = $shipments;
        $this->paymentMethodId = $paymentMethodId;
        $this->comment = $comment;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    /** @return array|OrderItemUnitRefund[] */
    public function units(): array
    {
        return $this->units;
    }

    /** @return array|ShipmentRefund[] */
    public function shipments(): array
    {
        return $this->shipments;
    }

    public function paymentMethodId(): int
    {
        return $this->paymentMethodId;
    }

    public function comment(): string
    {
        return $this->comment;
    }
}

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

use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

class RefundUnits
{
    private array $shipments = [];

    public function __construct(
        private string $orderNumber,
        private array $units,
        private int|array $paymentMethodId,
        private string|int $comment,
    ) {
        $args = func_get_args();

        if (is_array($paymentMethodId)) {
            if (!isset($args[4])) {
                throw new \InvalidArgumentException('The 5th argument must be present.');
            }

            $this->shipments = $paymentMethodId;
            /** @phpstan-ignore-next-line */
            $this->paymentMethodId = $comment;
            $this->comment = $args[4];

            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Passing an array as a 3th argument of "%s" constructor is deprecated and will be removed in 2.0.', self::class));
        }

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

    /**
     * @deprecated since 1.4, to be removed in 2.0. Use "units" method instead.
     *
     * @return array|ShipmentRefund[]
     */
    public function shipments(): array
    {
        trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('The "%s::shipments" method is deprecated and will be removed in 2.0.', self::class));

        return $this->shipments;
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

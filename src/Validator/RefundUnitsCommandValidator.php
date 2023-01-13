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

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefunding;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class RefundUnitsCommandValidator implements RefundUnitsCommandValidatorInterface
{
    private OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker;

    private RefundAmountValidatorInterface $refundAmountValidator;

    public function __construct(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefundAmountValidatorInterface $refundAmountValidator,
    ) {
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
        $this->refundAmountValidator = $refundAmountValidator;
    }

    public function validate(RefundUnits $command): void
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($command->orderNumber())) {
            throw OrderNotAvailableForRefunding::withOrderNumber($command->orderNumber());
        }

        $units = $command->units();
        $this->refundAmountValidator->validateUnits(array_filter($units, fn (UnitRefundInterface $unitRefund) => $unitRefund instanceof OrderItemUnitRefund), RefundType::orderItemUnit());
        $this->refundAmountValidator->validateUnits(array_filter($units, fn (UnitRefundInterface $unitRefund) => $unitRefund instanceof ShipmentRefund), RefundType::shipment());
    }
}

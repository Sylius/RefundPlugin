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

final class RefundUnitsCommandValidator implements RefundUnitsCommandValidatorInterface
{
    public function __construct(
        private OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        private RefundAmountValidatorInterface $refundAmountValidator,
        /** @var iterable<UnitRefundsBelongingToOrderValidatorInterface> */
        private iterable $refundUnitsBelongingToOrderValidators,
    ) {
    }

    public function validate(RefundUnits $command): void
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($command->orderNumber())) {
            throw OrderNotAvailableForRefunding::withOrderNumber($command->orderNumber());
        }

        $units = $command->units();

        foreach ($this->refundUnitsBelongingToOrderValidators as $refundUnitsBelongToOrderValidator) {
            $refundUnitsBelongToOrderValidator->validateUnits($units, $command->orderNumber());
        }

        $this->refundAmountValidator->validateUnits($units);
    }
}

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

use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Webmozart\Assert\Assert;

final class RefundAmountValidator implements RefundAmountValidatorInterface
{
    public function __construct(private RemainingTotalProviderInterface $remainingTotalProvider)
    {
    }

    public function validateUnits(array $unitRefunds): void
    {
        Assert::allIsInstanceOf($unitRefunds, UnitRefundInterface::class);

        /** @var UnitRefundInterface $unitRefund */
        foreach ($unitRefunds as $unitRefund) {
            if ($unitRefund->total() <= 0) {
                throw InvalidRefundAmount::withValidationConstraint(
                    RefundUnitsValidationConstraintMessages::REFUND_AMOUNT_MUST_BE_GREATER_THAN_ZERO,
                );
            }

            $unitRefundedTotal = $this->remainingTotalProvider->getTotalLeftToRefund(
                $unitRefund->id(),
                $unitRefund->type(),
            );

            if ($unitRefund->total() > $unitRefundedTotal) {
                throw InvalidRefundAmount::withValidationConstraint(
                    RefundUnitsValidationConstraintMessages::REFUND_AMOUNT_MUST_BE_LESS_THAN_AVAILABLE,
                );
            }
        }
    }
}

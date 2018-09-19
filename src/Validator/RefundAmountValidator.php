<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Exception\InvalidRefundAmountException;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Normalizer\MultipleMessagesNormalizerInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundAmountValidator implements RefundAmountValidatorInterface
{
    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    /** @var MultipleMessagesNormalizerInterface */
    private $multipleMessagesNormalizer;

    public function __construct(
        RemainingTotalProviderInterface $unitRefundedTotalProvider,
        MultipleMessagesNormalizerInterface $multipleMessagesNormalizer
    ) {
        $this->remainingTotalProvider = $unitRefundedTotalProvider;
        $this->multipleMessagesNormalizer = $multipleMessagesNormalizer;
    }

    public function validateUnits(array $refunds, RefundType $refundType): void
    {
        /** @var UnitRefundInterface $refund */
        foreach ($refunds as $refund) {
            if ($refund->total() <= 0) {
                throw InvalidRefundAmountException::withValidationConstraint(
                    RefundUnitsValidationConstraintMessages::REFUND_AMOUNT_MUST_BE_GREATER_THAN_ZERO
                );
            }

            $unitRefundedTotal = $this->remainingTotalProvider->getTotalLeftToRefund($refund->id(), $refundType);

            if ($refund->total() > $unitRefundedTotal) {
                throw InvalidRefundAmountException::withValidationConstraint(
                    RefundUnitsValidationConstraintMessages::REFUND_AMOUNT_MUST_BE_LESS_THAN_AVAILABLE
                );
            }
        }
    }
}

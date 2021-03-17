<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefunding;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundUnitsCommandValidator implements RefundUnitsCommandValidatorInterface
{
    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundingAvailabilityChecker;

    /** @var RefundAmountValidatorInterface */
    private $refundAmountValidator;

    public function __construct(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefundAmountValidatorInterface $refundAmountValidator
    ) {
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
        $this->refundAmountValidator = $refundAmountValidator;
    }

    public function validate(RefundUnits $command): void
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($command->orderNumber())) {
            throw OrderNotAvailableForRefunding::withOrderNumber($command->orderNumber());
        }

        $this->refundAmountValidator->validateUnits($command->units(), RefundType::orderItemUnit());
        $this->refundAmountValidator->validateUnits($command->shipments(), RefundType::shipment());
    }
}

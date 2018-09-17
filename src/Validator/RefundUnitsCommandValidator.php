<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\CreditMemoCommentTooLongException;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;

final class RefundUnitsCommandValidator
{
    private const MAX_COMMENT_LENGTH = 256;

    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundingAvailabilityChecker;

    public function __construct(OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker)
    {
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
    }

    /**
     * @throws OrderNotAvailableForRefundingException|CreditMemoCommentTooLongException
     */
    public function validate(RefundUnits $command): void
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($command->orderNumber())) {
            throw OrderNotAvailableForRefundingException::withOrderNumber($command->orderNumber());
        }

        if (strlen($command->comment()) > self::MAX_COMMENT_LENGTH) {
            throw new CreditMemoCommentTooLongException();
        }
    }
}

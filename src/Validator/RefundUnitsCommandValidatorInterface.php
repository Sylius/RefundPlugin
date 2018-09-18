<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\CreditMemoCommentTooLongException;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;

interface RefundUnitsCommandValidatorInterface
{
    /**
     * @throws OrderNotAvailableForRefundingException|CreditMemoCommentTooLongException
     */
    public function validate(RefundUnits $command): void;
}

<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

final class RefundUnitsValidationConstraintMessages
{
    public const REFUND_AMOUNT_MUST_BE_LESS_THAN_AVAILABLE = 'sylius_refund.refund_amount_must_be_less';

    public const REFUND_AMOUNT_MUST_BE_GREATER_THAN_ZERO = 'sylius_refund.refund_amount_must_be_greater';

    private function __construct()
    {
    }
}

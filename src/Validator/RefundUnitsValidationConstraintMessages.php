<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Validator;

final class RefundUnitsValidationConstraintMessages
{
    public const REFUND_AMOUNT_MUST_BE_LESS_THAN_AVAILABLE = 'sylius_refund.refund_amount_must_be_less';

    public const REFUND_AMOUNT_MUST_BE_GREATER_THAN_ZERO = 'sylius_refund.refund_amount_must_be_greater';

    public const REFUND_UNITS_MUST_BELONG_TO_ORDER = 'sylius_refund.refund_units_must_belong_to_order';

    private function __construct()
    {
    }
}

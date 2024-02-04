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

namespace Sylius\RefundPlugin\StateResolver;

final class RefundPaymentTransitions
{
    public const GRAPH = 'sylius_refund_refund_payment';

    public const TRANSITION_COMPLETE = 'complete';

    private function __construct()
    {
    }
}

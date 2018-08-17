<?php

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

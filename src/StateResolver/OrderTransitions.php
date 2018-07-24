<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

final class OrderTransitions
{
    public const GRAPH = 'sylius_order';

    public const TRANSITION_REFUND = 'refund';

    public const STATE_FULLY_REFUNDED = 'fully_refunded';

    private function __construct()
    {
    }
}

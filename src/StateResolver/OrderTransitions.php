<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

final class OrderTransitions
{
    public const GRAPH = 'sylius_order';

    public const REFUND = 'refund';

    private function __construct()
    {
    }
}

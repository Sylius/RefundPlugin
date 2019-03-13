<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Sylius\RefundPlugin\Event\UnitRefunded;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface;

final class UnitRefundedEventListener
{
    /** @var OrderPartiallyRefundedStateResolverInterface */
    private $orderPartiallyRefundedStateResolver;

    public function __construct(OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver)
    {
        $this->orderPartiallyRefundedStateResolver = $orderPartiallyRefundedStateResolver;
    }

    public function __invoke(UnitRefunded $unitRefunded): void
    {
        $this->orderPartiallyRefundedStateResolver->resolve($unitRefunded->orderNumber());
    }
}

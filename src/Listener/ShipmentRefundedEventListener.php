<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface;

final class ShipmentRefundedEventListener
{
    /** @var OrderPartiallyRefundedStateResolverInterface */
    private $orderPartiallyRefundedStateResolver;

    public function __construct(OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver)
    {
        $this->orderPartiallyRefundedStateResolver = $orderPartiallyRefundedStateResolver;
    }

    public function __invoke(ShipmentRefunded $shipmentRefunded): void
    {
        $this->orderPartiallyRefundedStateResolver->resolve($shipmentRefunded->orderNumber());
    }
}

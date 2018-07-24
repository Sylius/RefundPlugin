<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class UnitsRefundedEventListener
{
    /** @var Session */
    private $session;

    /** @var OrderFullyRefundedStateResolverInterface */
    private $orderFullyRefundedStateResolver;

    public function __construct(
        Session $session,
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver
    ) {
        $this->session = $session;
        $this->orderFullyRefundedStateResolver = $orderFullyRefundedStateResolver;
    }

    public function __invoke(UnitsRefunded $event): void
    {
        $this->orderFullyRefundedStateResolver->resolve($event->orderNumber());
        $this->session->getFlashBag()->add('success', 'sylius_refund.units_successfully_refunded');
    }
}

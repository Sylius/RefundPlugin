<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Sylius\RefundPlugin\Event\UnitsRefunded;
use Symfony\Component\HttpFoundation\Session\Session;

final class UnitsRefundedEventListener
{
    /** @var Session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke(UnitsRefunded $event): void
    {
        $this->session->getFlashBag()->add('success', 'sylius_refund.units_successfully_refunded');
    }
}

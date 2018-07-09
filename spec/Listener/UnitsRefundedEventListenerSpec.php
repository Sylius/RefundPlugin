<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class UnitsRefundedEventListenerSpec extends ObjectBehavior
{
    function let(Session $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_listens_to_units_refunded_event_and_add_success_flash_after_it_occurs(
        Session $session,
        FlashBagInterface $flashBag
    ): void {
        $session->getFlashBag()->willReturn($flashBag);

        $flashBag->add('success', 'sylius_refund.units_successfully_refunded')->shouldBeCalled();

        $this(new UnitsRefunded('000222', [1, 2], 1000));
    }
}

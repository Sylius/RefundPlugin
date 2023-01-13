<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\UnitRefundInterface;

final class RefundUnitsSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_refund_specific_units(UnitRefundInterface $orderItemUnit, UnitRefundInterface $shipmentUnit): void
    {
        $unitRefunds = [$orderItemUnit, $shipmentUnit];

        $this->beConstructedWith('000222', $unitRefunds, 1, 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->units()->shouldReturn($unitRefunds);
        $this->paymentMethodId()->shouldReturn(1);
        $this->comment()->shouldReturn('Comment');
    }

    function it_throws_an_exception_if_units_are_not_an_instance_of_unit_refund_interface(): void
    {
        $this->beConstructedWith('000222', [new \stdClass()], 1, 'Comment');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}

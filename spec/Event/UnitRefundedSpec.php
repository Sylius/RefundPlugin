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

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;

final class UnitRefundedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_unit_has_been_refunded(): void
    {
        $this->beConstructedWith('000222', 1, 1000);

        $this->orderNumber()->shouldReturn('000222');
        $this->unitId()->shouldReturn(1);
        $this->amount()->shouldReturn(1000);
    }
}

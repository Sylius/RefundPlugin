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
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;

final class GenerateCreditMemoSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_generate_credit_memo(): void
    {
        $unitRefunds = [
            new OrderItemUnitRefund(1, 1000),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(1, 1000),
        ];

        $this->beConstructedWith('000222', 1000, $unitRefunds, 'Comment');

        $this->orderNumber()->shouldReturn('000222');
        $this->total()->shouldReturn(1000);
        $this->units()->shouldReturn($unitRefunds);
        $this->comment()->shouldReturn('Comment');
    }
}

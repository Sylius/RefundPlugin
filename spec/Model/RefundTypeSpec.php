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

namespace spec\Sylius\RefundPlugin\Model;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundTypeSpec extends ObjectBehavior
{
    function it_can_be_order_item_unit_type(): void
    {
        $this->beConstructedThrough('orderItemUnit');
        $this->getValue()->shouldReturn(RefundType::ORDER_ITEM_UNIT);
    }

    function it_can_be_shipment_type(): void
    {
        $this->beConstructedThrough('shipment');
        $this->getValue()->shouldReturn(RefundType::SHIPMENT);
    }
}

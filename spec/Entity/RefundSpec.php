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

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('000666', 1000, 3, RefundType::orderItemUnit());
    }

    public function it_implements_refund_interface(): void
    {
        $this->shouldImplement(RefundInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_order_number(): void
    {
        $this->getOrderNumber()->shouldReturn('000666');
    }

    public function it_has_amount(): void
    {
        $this->getAmount()->shouldReturn(1000);
    }

    public function it_has_refunded_unit_id(): void
    {
        $this->getRefundedUnitId()->shouldReturn(3);
    }

    public function it_has_type(): void
    {
        $this->getType()->shouldBeLike(RefundType::orderItemUnit());
    }
}

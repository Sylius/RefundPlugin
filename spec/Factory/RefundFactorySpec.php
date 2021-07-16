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

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\Refund;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(Refund::class);
    }

    function it_implements_refund_factory_interface(): void
    {
        $this->shouldImplement(RefundFactoryInterface::class);
    }

    function it_allows_to_create_refund_with_given_data(OrderInterface $order): void
    {
        $this
            ->createWithData($order, 1, 1000, RefundType::orderItemUnit())
            ->shouldBeLike(new Refund($order->getWrappedObject(), 1000, 1, RefundType::orderItemUnit()))
        ;
    }

    function it_throws_exception_if_it_tries_to_create_default_refund_without_data(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createNew');
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\Refund;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;

final class RefundFactorySpec extends ObjectBehavior
{
    function it_implements_refund_factory_interface(): void
    {
        $this->shouldImplement(RefundFactoryInterface::class);
    }

    function it_allows_to_create_refund_with_given_data(): void
    {
        $this->createWithData('0001', 1, 1000)->shouldBeLike(new Refund('0001', 1000, 1));
    }
}

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
use Sylius\RefundPlugin\Entity\Refund;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundFactorySpec extends ObjectBehavior
{
    public function it_implements_refund_factory_interface(): void
    {
        $this->shouldImplement(RefundFactoryInterface::class);
    }

    public function it_allows_to_create_refund_with_given_data(): void
    {
        $this
            ->createWithData('0001', 1, 1000, RefundType::orderItemUnit())
            ->shouldBeLike(new Refund('0001', 1000, 1, RefundType::orderItemUnit()))
        ;
    }
}

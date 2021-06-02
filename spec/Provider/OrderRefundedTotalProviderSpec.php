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

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderRefundedTotalProviderSpec extends ObjectBehavior
{
    public function let(RepositoryInterface $refundRepository): void
    {
        $this->beConstructedWith($refundRepository);
    }

    public function it_implements_order_refunded_total_provider_interface(): void
    {
        $this->shouldImplement(OrderRefundedTotalProviderInterface::class);
    }

    public function it_returns_refunded_total_of_order_with_given_number(
        RepositoryInterface $refundRepository,
        RefundInterface $firstRefund,
        RefundInterface $secondRefund
    ): void {
        $refundRepository->findBy(['orderNumber' => '000222'])->willReturn([$firstRefund, $secondRefund]);

        $firstRefund->getAmount()->willReturn(1000);
        $secondRefund->getAmount()->willReturn(500);

        $this->__invoke('000222')->shouldReturn(1500);
    }
}

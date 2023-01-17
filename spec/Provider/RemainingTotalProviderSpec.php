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
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\DelegatingRefundUnitTotalProviderInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RemainingTotalProviderSpec extends ObjectBehavior
{
    function let(
        DelegatingRefundUnitTotalProviderInterface $refundUnitTotalProvider,
        RepositoryInterface $refundRepository,
    ): void {
        $this->beConstructedWith($refundUnitTotalProvider, $refundRepository);
    }

    function it_implements_remaining_total_provider_interface(): void
    {
        $this->shouldImplement(RemainingTotalProviderInterface::class);
    }

    function it_returns_remaining_total_to_refund(
        DelegatingRefundUnitTotalProviderInterface $refundUnitTotalProvider,
        RepositoryInterface $refundRepository,
        RefundInterface $refund,
    ): void {
        $refundType = RefundType::orderItemUnit();

        $refundUnitTotalProvider->getRefundUnitTotal(1, $refundType)->willReturn(1000);
        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([$refund])
        ;

        $refund->getAmount()->willReturn(500);

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(500);
    }

    function it_returns_unit_total_if_there_is_no_refund_for_this_unit_yet(
        DelegatingRefundUnitTotalProviderInterface $refundUnitTotalProvider,
        RepositoryInterface $refundRepository,
    ): void {
        $refundType = RefundType::orderItemUnit();

        $refundUnitTotalProvider->getRefundUnitTotal(1, $refundType)->willReturn(1000);

        $refundRepository
            ->findBy(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([])
        ;

        $this->getTotalLeftToRefund(1, $refundType)->shouldReturn(1000);
    }
}

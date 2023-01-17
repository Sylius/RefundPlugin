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
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\DelegatingRefundUnitTotalProviderInterface;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class DelegatingRefundUnitTotalProviderSpec extends ObjectBehavior
{
    function let(
        ServiceProviderInterface $refundUnitTotalProviders,
    ): void {
        $this->beConstructedWith($refundUnitTotalProviders);
    }

    function it_is_refund_unit_total_provider(): void
    {
        $this->shouldImplement(DelegatingRefundUnitTotalProviderInterface::class);
    }

    function it_delegates_returning_unit_total_to_refund(
        ServiceProviderInterface $refundUnitTotalProviders,
        RefundUnitTotalProviderInterface $refundUnitTotalProvider,
    ): void {
        $refundType = RefundType::orderItemUnit();

        $refundUnitTotalProviders->get('order_item_unit')->willReturn($refundUnitTotalProvider);
        $refundUnitTotalProvider->getRefundUnitTotal(1)->willReturn(1000);

        $this->getRefundUnitTotal(1, $refundType)->shouldReturn(1000);
    }
}

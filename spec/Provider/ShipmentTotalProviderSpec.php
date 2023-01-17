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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;

final class ShipmentTotalProviderSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $adjustmentRepository,
    ): void {
        $this->beConstructedWith($adjustmentRepository);
    }

    function it_is_refund_unit_total_provider(): void
    {
        $this->shouldImplement(RefundUnitTotalProviderInterface::class);
    }

    function it_returns_shipment_total_to_refund(
        RepositoryInterface $adjustmentRepository,
        AdjustmentInterface $shippingAdjustment,
        ShipmentInterface $shipment,
    ): void {
        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;

        $shippingAdjustment->getShipment()->willReturn($shipment);
        $shipment->getAdjustmentsTotal()->willReturn(1000);

        $this->getRefundUnitTotal(1)->shouldReturn(1000);
    }

    function it_throws_exception_if_there_is_no_shipment_with_given_id(
        RepositoryInterface $adjustmentRepository,
    ): void {
        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn(null)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getRefundUnitTotal', [1])
        ;
    }
}

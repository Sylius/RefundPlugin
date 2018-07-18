<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Provider\RefundedShipmentFeeProviderInterface;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class RefundedShipmentFeeProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $adjustmentRepository): void
    {
        $this->beConstructedWith($adjustmentRepository);
    }

    function it_implements_refunded_shipment_fee_provider_interface()
    {
        $this->shouldImplement(RefundedShipmentFeeProviderInterface::class);
    }

    function it_returns_fee_from_shipping_adjustment(
        RepositoryInterface $adjustmentRepository,
        AdjustmentInterface $shippingAdjustment
    ): void {
        $adjustmentRepository->find(1)->willReturn($shippingAdjustment);
        $shippingAdjustment->getAmount()->willReturn(1000);

        $this->getFeeOfShipment(1)->shouldReturn(1000);
    }

    function it_throws_exception_if_there_is_no_adjustment_with_given_id(
        RepositoryInterface $adjustmentRepository
    ): void {
        $adjustmentRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getFeeOfShipment', [1])
        ;
    }
}

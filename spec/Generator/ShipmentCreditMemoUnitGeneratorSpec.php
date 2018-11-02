<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Generator\CreditMemoUnitGeneratorInterface;

final class ShipmentCreditMemoUnitGeneratorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $adjustmentRepository): void
    {
        $this->beConstructedWith($adjustmentRepository);
    }

    function it_implements_credit_memo_unit_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoUnitGeneratorInterface::class);
    }

    function it_generates_credit_memo_unit_from_shipping_adjustment(
        RepositoryInterface $adjustmentRepository,
        AdjustmentInterface $shippingAdjustment
    ): void {
        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;

        $shippingAdjustment->getLabel()->willReturn('Galaxy post');
        $shippingAdjustment->getAmount()->willReturn(1000);

        $this->generate(1)->shouldBeLike(new CreditMemoUnit('Galaxy post', 1000, 0));
    }

    function it_throws_exception_if_there_is_no_shipping_adjustment_with_given_id(
        RepositoryInterface $adjustmentRepository
    ): void {
        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn(null)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('generate', [1])
        ;
    }

    function it_throws_exception_if_refund_amount_is_higher_than_shipping_amount(
        RepositoryInterface $adjustmentRepository,
        AdjustmentInterface $shippingAdjustment
    ): void {
        $adjustmentRepository
            ->findOneBy(['id' => 1, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ->willReturn($shippingAdjustment)
        ;

        $shippingAdjustment->getAmount()->willReturn(1000);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('generate', [1, 1001])
        ;
    }
}

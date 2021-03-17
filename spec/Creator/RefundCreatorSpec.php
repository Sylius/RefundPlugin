<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Creator;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefunded;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundCreatorSpec extends ObjectBehavior
{
    function let(
        RefundFactoryInterface $refundFactory,
        RemainingTotalProviderInterface $remainingTotalProvider,
        ObjectManager $refundEntityManager
    ): void {
        $this->beConstructedWith(
            $refundFactory,
            $remainingTotalProvider,
            $refundEntityManager
        );
    }

    function it_implements_refund_creator_interface(): void
    {
        $this->shouldImplement(RefundCreatorInterface::class);
    }

    function it_creates_refund_with_given_data_and_save_it_in_database(
        RefundFactoryInterface $refundFactory,
        RemainingTotalProviderInterface $remainingTotalProvider,
        ObjectManager $refundEntityManager,
        RefundInterface $refund
    ): void {
        $refundType = RefundType::shipment();

        $remainingTotalProvider->getTotalLeftToRefund(1, $refundType)->willReturn(1000);

        $refundFactory->createWithData('000222', 1, 1000, RefundType::shipment())->willReturn($refund);

        $refundEntityManager->persist($refund)->shouldBeCalled();
        $refundEntityManager->flush()->shouldBeCalled();

        $this('000222', 1, 1000, $refundType);
    }

    function it_throws_exception_if_unit_has_already_been_refunded(
        RemainingTotalProviderInterface $remainingTotalProvider
    ): void {
        $refundType = RefundType::shipment();

        $remainingTotalProvider->getTotalLeftToRefund(1, $refundType)->willReturn(0);

        $this
            ->shouldThrow(UnitAlreadyRefunded::class)
            ->during('__invoke', ['000222', 1, 1000, $refundType])
        ;
    }
}

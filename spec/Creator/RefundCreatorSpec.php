<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefundedException;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;

final class RefundCreatorSpec extends ObjectBehavior
{
    function let(
        RefundFactoryInterface $refundFactory,
        RepositoryInterface $refundRepository,
        ObjectManager $refundManager
    ): void {
        $this->beConstructedWith($refundFactory, $refundRepository, $refundManager);
    }

    function it_implements_refund_creator_interface()
    {
        $this->shouldImplement(RefundCreatorInterface::class);
    }

    function it_creates_refund_with_given_data_and_save_it_in_database(
        RefundFactoryInterface $refundFactory,
        RepositoryInterface $refundRepository,
        ObjectManager $refundManager,
        RefundInterface $refund
    ) {
        $refundRepository->findOneBy(['orderNumber' => '000222', 'refundedUnitId' => 1])->willReturn(null);

        $refundFactory->createWithData('000222', 1, 1000)->willReturn($refund);

        $refundManager->persist($refund)->shouldBeCalled();
        $refundManager->flush()->shouldBeCalled();

        $this('000222', 1, 1000);
    }

    function it_throws_exception_if_unit_has_already_been_refunded(
        RepositoryInterface $refundRepository,
        RefundInterface $refund
    ) {
        $refundRepository->findOneBy(['orderNumber' => '000222', 'refundedUnitId' => 1])->willReturn($refund);

        $this
            ->shouldThrow(UnitAlreadyRefundedException::class)
            ->during('__invoke', ['000222', 1, 1000])
        ;
    }
}

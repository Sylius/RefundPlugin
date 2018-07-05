<?php

namespace spec\Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;

final class RefundCreatorSpec extends ObjectBehavior
{
    function let(
        RefundFactoryInterface $refundFactory,
        ObjectManager $refundManager
    ): void {
        $this->beConstructedWith($refundFactory, $refundManager);
    }

    function it_implements_refund_creator_interface()
    {
        $this->shouldImplement(RefundCreatorInterface::class);
    }

    function it_creates_refund_with_given_data_and_save_it_in_database(
        RefundFactoryInterface $refundFactory,
        ObjectManager $refundManager,
        RefundInterface $refund
    ) {
        $refundFactory->createWithData('000222', 1, 1000)->willReturn($refund);

        $refundManager->persist($refund)->shouldBeCalled();
        $refundManager->flush()->shouldBeCalled();

        $this('000222', 1, 1000);
    }
}

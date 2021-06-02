<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoSequenceInterface;
use Sylius\RefundPlugin\Factory\CreditMemoSequenceFactoryInterface;
use Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface;

final class SequentialCreditMemoNumberGeneratorSpec extends ObjectBehavior
{
    public function let(
        ObjectRepository $sequenceRepository,
        CreditMemoSequenceFactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager
    ): void {
        $this->beConstructedWith(
            $sequenceRepository,
            $sequenceFactory,
            $sequenceManager,
            1,
            9
        );
    }

    public function it_is_number_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoNumberGeneratorInterface::class);
    }

    public function it_generates_sequential_number(
        ObjectRepository $sequenceRepository,
        EntityManagerInterface $sequenceManager,
        CreditMemoSequenceInterface $sequence,
        \DateTimeImmutable $issuedAt,
        OrderInterface $order
    ): void {
        $issuedAt->format('Y/m')->willReturn('2018/05');

        $sequenceRepository->findOneBy([])->willReturn($sequence);

        $sequence->getVersion()->willReturn(1);
        $sequence->getIndex()->willReturn(5);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate($order, $issuedAt)->shouldReturn('2018/05/000000006');
    }

    public function it_generates_invoice_number_when_sequence_is_null(
        ObjectRepository $sequenceRepository,
        CreditMemoSequenceFactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        CreditMemoSequenceInterface $sequence,
        \DateTimeImmutable $issuedAt,
        OrderInterface $order
    ): void {
        $issuedAt->format('Y/m')->willReturn('2018/05');

        $sequenceRepository->findOneBy([])->willReturn(null);

        $sequenceFactory->createNew()->willReturn($sequence);

        $sequenceManager->persist($sequence)->shouldBeCalled();

        $sequence->getVersion()->willReturn(1);
        $sequence->getIndex()->willReturn(0);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate($order, $issuedAt)->shouldReturn('2018/05/000000001');
    }
}

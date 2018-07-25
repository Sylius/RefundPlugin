<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\SequenceInterface;
use Sylius\RefundPlugin\Factory\SequenceFactoryInterface;
use Sylius\RefundPlugin\Generator\NumberGenerator;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;

final class SequentialNumberGeneratorSpec extends ObjectBehavior
{
    function let(
        ObjectRepository $sequenceRepository,
        SequenceFactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        CurrentDateTimeProviderInterface $currentDateTimeProvider
    ): void {
        $this->beConstructedWith(
            $sequenceRepository,
            $sequenceFactory,
            $sequenceManager,
            $currentDateTimeProvider,
            1,
            9
        );
    }

    function it_is_number_generator_interface(): void
    {
        $this->shouldImplement(NumberGenerator::class);
    }

    function it_generates_sequential_number(
        ObjectRepository $sequenceRepository,
        EntityManagerInterface $sequenceManager,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        SequenceInterface $sequence,
        \DateTime $now
    ): void {
        $currentDateTimeProvider->now()->willReturn($now);
        $now->format('Y/m')->willReturn('2018/05');

        $sequenceRepository->findOneBy([])->willReturn($sequence);

        $sequence->getVersion()->willReturn(1);
        $sequence->getIndex()->willReturn(5);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate()->shouldReturn('2018/05/000000006');
    }

    function it_generates_invoice_number_when_sequence_is_null(
        ObjectRepository $sequenceRepository,
        SequenceFactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        SequenceInterface $sequence,
        \DateTime $now
    ): void {
        $currentDateTimeProvider->now()->willReturn($now);
        $now->format('Y/m')->willReturn('2018/05');

        $sequenceRepository->findOneBy([])->willReturn(null);

        $sequenceFactory->createNew()->willReturn($sequence);

        $sequenceManager->persist($sequence)->shouldBeCalled();

        $sequence->getVersion()->willReturn(1);
        $sequence->getIndex()->willReturn(0);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate()->shouldReturn('2018/05/000000001');
    }
}

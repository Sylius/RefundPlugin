<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\RefundPlugin\Entity\SequenceInterface;
use Sylius\RefundPlugin\Factory\SequenceFactoryInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;

final class SequentialNumberGenerator implements NumberGenerator
{
    /** @var ObjectRepository */
    private $sequenceRepository;

    /** @var SequenceFactoryInterface */
    private $sequenceFactory;

    /** @var EntityManagerInterface */
    private $sequenceManager;

    /** @var CurrentDateTimeProviderInterface */
    private $currentDateTimeProvider;

    /** @var int */
    private $startNumber;

    /** @var int */
    private $numberLength;

    public function __construct(
        ObjectRepository $sequenceRepository,
        SequenceFactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        int $startNumber = 1,
        int $numberLength = 9
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
        $this->sequenceManager = $sequenceManager;
        $this->currentDateTimeProvider = $currentDateTimeProvider;
        $this->startNumber = $startNumber;
        $this->numberLength = $numberLength;
    }

    public function generate(): string
    {
        $identifierPrefix = $this->currentDateTimeProvider->now()->format('y/m') . '/';

        /** @var SequenceInterface $sequence */
        $sequence = $this->getSequence();

        $this->sequenceManager->lock($sequence, LockMode::OPTIMISTIC, $sequence->getVersion());

        $number = $this->generateNumber($sequence->getIndex());
        $sequence->incrementIndex();

        return $identifierPrefix . $number;
    }

    private function generateNumber(int $index): string
    {
        $number = $this->startNumber + $index;

        return str_pad((string) $number, $this->numberLength, '0', STR_PAD_LEFT);
    }

    private function getSequence(): SequenceInterface
    {
        /** @var SequenceInterface $sequence */
        $sequence = $this->sequenceRepository->findOneBy([]);

        if (null != $sequence) {
            return $sequence;
        }

        /** @var SequenceInterface $sequence */
        $sequence = $this->sequenceFactory->createNew();
        $this->sequenceManager->persist($sequence);

        return $sequence;
    }
}

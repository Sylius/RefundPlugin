<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoSequenceInterface;
use Sylius\RefundPlugin\Factory\CreditMemoSequenceFactoryInterface;

final class SequentialCreditMemoNumberGenerator implements CreditMemoNumberGeneratorInterface
{
    /** @var ObjectRepository */
    private $sequenceRepository;

    /** @var CreditMemoSequenceFactoryInterface */
    private $sequenceFactory;

    /** @var EntityManagerInterface */
    private $sequenceManager;

    /** @var int */
    private $startNumber;

    /** @var int */
    private $numberLength;

    public function __construct(
        ObjectRepository $sequenceRepository,
        CreditMemoSequenceFactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        int $startNumber = 1,
        int $numberLength = 9
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
        $this->sequenceManager = $sequenceManager;
        $this->startNumber = $startNumber;
        $this->numberLength = $numberLength;
    }

    public function generate(OrderInterface $order, \DateTimeInterface $issuedAt): string
    {
        $identifierPrefix = $issuedAt->format('Y/m') . '/';

        /** @var CreditMemoSequenceInterface $sequence */
        $sequence = $this->getSequence();

        $this->sequenceManager->lock($sequence, LockMode::OPTIMISTIC, $sequence->getVersion());

        $number = $this->generateNumber($sequence->getIndex());
        $sequence->incrementIndex();

        return $identifierPrefix . $number;
    }

    private function generateNumber(int $index): string
    {
        $number = $this->startNumber + $index;

        return str_pad((string) $number, $this->numberLength, '0', \STR_PAD_LEFT);
    }

    private function getSequence(): CreditMemoSequenceInterface
    {
        /** @var CreditMemoSequenceInterface|null $sequence */
        $sequence = $this->sequenceRepository->findOneBy([]);
        if (null !== $sequence) {
            return $sequence;
        }

        /** @var CreditMemoSequenceInterface $sequence */
        $sequence = $this->sequenceFactory->createNew();
        $this->sequenceManager->persist($sequence);

        return $sequence;
    }
}

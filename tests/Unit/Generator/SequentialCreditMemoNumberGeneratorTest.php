<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Tests\Unit\Generator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoSequenceInterface;
use Sylius\RefundPlugin\Factory\CreditMemoSequenceFactoryInterface;
use Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface;
use Sylius\RefundPlugin\Generator\SequentialCreditMemoNumberGenerator;

final class SequentialCreditMemoNumberGeneratorTest extends TestCase
{
    private SequentialCreditMemoNumberGenerator $generator;

    private ObjectRepository&MockObject $sequenceRepository;

    private CreditMemoSequenceFactoryInterface&MockObject $sequenceFactory;

    private EntityManagerInterface&MockObject $sequenceManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sequenceRepository = $this->createMock(ObjectRepository::class);
        $this->sequenceFactory = $this->createMock(CreditMemoSequenceFactoryInterface::class);
        $this->sequenceManager = $this->createMock(EntityManagerInterface::class);

        $this->generator = new SequentialCreditMemoNumberGenerator(
            $this->sequenceRepository,
            $this->sequenceFactory,
            $this->sequenceManager,
            1,
            9,
        );
    }

    public function testItImplementsCreditMemoNumberGeneratorInterface(): void
    {
        self::assertInstanceOf(CreditMemoNumberGeneratorInterface::class, $this->generator);
    }

    public function testItGeneratesSequentialNumber(): void
    {
        $sequence = $this->createMock(CreditMemoSequenceInterface::class);
        $issuedAt = $this->createMock(\DateTimeImmutable::class);
        $order = $this->createMock(OrderInterface::class);

        $issuedAt->expects(self::once())
            ->method('format')
            ->with('Y/m')
            ->willReturn('2018/05');

        $this->sequenceRepository->expects(self::once())
            ->method('findOneBy')
            ->with([])
            ->willReturn($sequence);

        $sequence->expects(self::once())
            ->method('getVersion')
            ->willReturn(1);

        $sequence->expects(self::once())
            ->method('getIndex')
            ->willReturn(5);

        $this->sequenceManager->expects(self::once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence->expects(self::once())
            ->method('incrementIndex');

        $result = $this->generator->generate($order, $issuedAt);

        self::assertSame('2018/05/000000006', $result);
    }

    public function testItGeneratesInvoiceNumberWhenSequenceIsNull(): void
    {
        $sequence = $this->createMock(CreditMemoSequenceInterface::class);
        $issuedAt = $this->createMock(\DateTimeImmutable::class);
        $order = $this->createMock(OrderInterface::class);

        $issuedAt->expects(self::once())
            ->method('format')
            ->with('Y/m')
            ->willReturn('2018/05');

        $this->sequenceRepository->expects(self::once())
            ->method('findOneBy')
            ->with([])
            ->willReturn(null);

        $this->sequenceFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($sequence);

        $this->sequenceManager->expects(self::once())
            ->method('persist')
            ->with($sequence);

        $sequence->expects(self::once())
            ->method('getVersion')
            ->willReturn(1);

        $sequence->expects(self::once())
            ->method('getIndex')
            ->willReturn(0);

        $this->sequenceManager->expects(self::once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence->expects(self::once())
            ->method('incrementIndex');

        $result = $this->generator->generate($order, $issuedAt);

        self::assertSame('2018/05/000000001', $result);
    }
}

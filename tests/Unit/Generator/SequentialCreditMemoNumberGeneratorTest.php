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
    
    /** @var ObjectRepository&MockObject */
    private ObjectRepository $sequenceRepository;
    
    /** @var CreditMemoSequenceFactoryInterface&MockObject */
    private CreditMemoSequenceFactoryInterface $sequenceFactory;
    
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $sequenceManager;

    protected function setUp(): void
    {
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
        $this->assertInstanceOf(CreditMemoNumberGeneratorInterface::class, $this->generator);
    }

    public function testItGeneratesSequentialNumber(): void
    {
        $sequence = $this->createMock(CreditMemoSequenceInterface::class);
        $issuedAt = $this->createMock(\DateTimeImmutable::class);
        $order = $this->createMock(OrderInterface::class);

        $issuedAt->expects($this->once())
            ->method('format')
            ->with('Y/m')
            ->willReturn('2018/05');

        $this->sequenceRepository->expects($this->once())
            ->method('findOneBy')
            ->with([])
            ->willReturn($sequence);

        $sequence->expects($this->once())
            ->method('getVersion')
            ->willReturn(1);

        $sequence->expects($this->once())
            ->method('getIndex')
            ->willReturn(5);

        $this->sequenceManager->expects($this->once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence->expects($this->once())
            ->method('incrementIndex');

        $result = $this->generator->generate($order, $issuedAt);

        $this->assertSame('2018/05/000000006', $result);
    }

    public function testItGeneratesInvoiceNumberWhenSequenceIsNull(): void
    {
        $sequence = $this->createMock(CreditMemoSequenceInterface::class);
        $issuedAt = $this->createMock(\DateTimeImmutable::class);
        $order = $this->createMock(OrderInterface::class);

        $issuedAt->expects($this->once())
            ->method('format')
            ->with('Y/m')
            ->willReturn('2018/05');

        $this->sequenceRepository->expects($this->once())
            ->method('findOneBy')
            ->with([])
            ->willReturn(null);

        $this->sequenceFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($sequence);

        $this->sequenceManager->expects($this->once())
            ->method('persist')
            ->with($sequence);

        $sequence->expects($this->once())
            ->method('getVersion')
            ->willReturn(1);

        $sequence->expects($this->once())
            ->method('getIndex')
            ->willReturn(0);

        $this->sequenceManager->expects($this->once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence->expects($this->once())
            ->method('incrementIndex');

        $result = $this->generator->generate($order, $issuedAt);

        $this->assertSame('2018/05/000000001', $result);
    }
}
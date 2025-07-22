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

namespace Sylius\RefundPlugin\Tests\Unit\Creator;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Creator\RefundCreator;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefunded;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundCreatorTest extends TestCase
{
    private RefundFactoryInterface&MockObject $refundFactory;
    private RemainingTotalProviderInterface&MockObject $remainingTotalProvider;
    private OrderRepositoryInterface&MockObject $orderRepository;
    private EntityManagerInterface&MockObject $refundEntityManager;
    private RefundCreator $refundCreator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refundFactory = $this->createMock(RefundFactoryInterface::class);
        $this->remainingTotalProvider = $this->createMock(RemainingTotalProviderInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->refundEntityManager = $this->createMock(EntityManagerInterface::class);

        $this->refundCreator = new RefundCreator(
            $this->refundFactory,
            $this->remainingTotalProvider,
            $this->orderRepository,
            $this->refundEntityManager,
        );
    }

    public function testItImplementsRefundCreatorInterface(): void
    {
        self::assertInstanceOf(RefundCreatorInterface::class, $this->refundCreator);
    }

    public function testItCreatesRefundWithGivenDataAndSaveItInDatabase(): void
    {
        $refundType = RefundType::shipment();
        $order = $this->createMock(OrderInterface::class);
        $refund = $this->createMock(RefundInterface::class);

        $this->orderRepository->expects(self::once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn($order);

        $this->remainingTotalProvider->expects(self::once())
            ->method('getTotalLeftToRefund')
            ->with(1, $refundType)
            ->willReturn(1000);

        $this->refundFactory->expects(self::once())
            ->method('createWithData')
            ->with($order, 1, 1000, RefundType::shipment())
            ->willReturn($refund);

        $this->refundEntityManager->expects(self::once())
            ->method('persist')
            ->with($refund);

        $this->refundEntityManager->expects(self::once())
            ->method('flush');

        ($this->refundCreator)('000222', 1, 1000, $refundType);
    }

    public function testItThrowsAnExceptionIfOrderWithGivenNumberDoesNotExist(): void
    {
        $refundType = RefundType::shipment();

        $this->orderRepository->expects(self::once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        ($this->refundCreator)('000222', 1, 1000, $refundType);
    }

    public function testItThrowsExceptionIfUnitHasAlreadyBeenRefunded(): void
    {
        $refundType = RefundType::shipment();
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository->expects(self::once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn($order);

        $this->remainingTotalProvider->expects(self::once())
            ->method('getTotalLeftToRefund')
            ->with(1, $refundType)
            ->willReturn(0);

        $this->expectException(UnitAlreadyRefunded::class);

        ($this->refundCreator)('000222', 1, 1000, $refundType);
    }
}

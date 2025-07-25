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

namespace Tests\Sylius\RefundPlugin\Unit\Provider;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProvider;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class RemainingTotalProviderTest extends TestCase
{
    /** @var ServiceProviderInterface<RefundUnitTotalProviderInterface>&MockObject */
    private ServiceProviderInterface&MockObject $refundUnitTotalProviders;

    private RepositoryInterface&MockObject $refundRepository;

    private RemainingTotalProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refundUnitTotalProviders = $this->createMock(ServiceProviderInterface::class);
        $this->refundRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new RemainingTotalProvider($this->refundUnitTotalProviders, $this->refundRepository);
    }

    #[Test]
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(RemainingTotalProvider::class, $this->provider);
    }

    #[Test]
    public function it_implements_remaining_total_provider_interface(): void
    {
        self::assertInstanceOf(RemainingTotalProviderInterface::class, $this->provider);
    }

    #[Test]
    public function it_returns_remaining_total_to_refund(): void
    {
        $refundType = RefundType::orderItemUnit();
        $refundUnitTotalProvider = $this->createMock(RefundUnitTotalProviderInterface::class);
        $refund = $this->createMock(RefundInterface::class);

        $this->refundUnitTotalProviders
            ->expects(self::once())
            ->method('get')
            ->with($refundType->getValue())
            ->willReturn($refundUnitTotalProvider);

        $refundUnitTotalProvider
            ->expects(self::once())
            ->method('getRefundUnitTotal')
            ->with(1)
            ->willReturn(1000);

        $this->refundRepository
            ->expects(self::once())
            ->method('findBy')
            ->with(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([$refund]);

        $refund
            ->expects(self::once())
            ->method('getAmount')
            ->willReturn(500);

        $result = $this->provider->getTotalLeftToRefund(1, $refundType);

        self::assertSame(500, $result);
    }

    #[Test]
    public function it_returns_unit_total_if_there_is_no_refund_for_this_unit_yet(): void
    {
        $refundType = RefundType::orderItemUnit();
        $refundUnitTotalProvider = $this->createMock(RefundUnitTotalProviderInterface::class);

        $this->refundUnitTotalProviders
            ->expects(self::once())
            ->method('get')
            ->with($refundType->getValue())
            ->willReturn($refundUnitTotalProvider);

        $refundUnitTotalProvider
            ->expects(self::once())
            ->method('getRefundUnitTotal')
            ->with(1)
            ->willReturn(1000);

        $this->refundRepository
            ->expects(self::once())
            ->method('findBy')
            ->with(['refundedUnitId' => 1, 'type' => $refundType->__toString()])
            ->willReturn([]);

        $result = $this->provider->getTotalLeftToRefund(1, $refundType);

        self::assertSame(1000, $result);
    }
}

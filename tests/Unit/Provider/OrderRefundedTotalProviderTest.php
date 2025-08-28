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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProvider;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderRefundedTotalProviderTest extends TestCase
{
    private RepositoryInterface&MockObject $refundRepository;

    private OrderRefundedTotalProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refundRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new OrderRefundedTotalProvider($this->refundRepository);
    }

    #[Test]
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(OrderRefundedTotalProvider::class, $this->provider);
    }

    #[Test]
    public function it_implements_order_refunded_total_provider_interface(): void
    {
        self::assertInstanceOf(OrderRefundedTotalProviderInterface::class, $this->provider);
    }

    #[Test]
    public function it_returns_refunded_total_of_order_with_given_number(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $firstRefund = $this->createMock(RefundInterface::class);
        $secondRefund = $this->createMock(RefundInterface::class);

        $this->refundRepository
            ->expects(self::once())
            ->method('findBy')
            ->with(['order' => $order])
            ->willReturn([$firstRefund, $secondRefund]);

        $firstRefund
            ->expects(self::once())
            ->method('getAmount')
            ->willReturn(1000);

        $secondRefund
            ->expects(self::once())
            ->method('getAmount')
            ->willReturn(500);

        $result = $this->provider->__invoke($order);

        self::assertSame(1500, $result);
    }
}

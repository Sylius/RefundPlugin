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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Provider\OrderItemUnitTotalProvider;
use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;

final class OrderItemUnitTotalProviderTest extends TestCase
{
    private RepositoryInterface&MockObject $orderItemUnitRepository;

    private OrderItemUnitTotalProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemUnitRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new OrderItemUnitTotalProvider($this->orderItemUnitRepository);
    }

    /** @test */
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(OrderItemUnitTotalProvider::class, $this->provider);
    }

    /** @test */
    public function it_is_refund_unit_total_provider(): void
    {
        self::assertInstanceOf(RefundUnitTotalProviderInterface::class, $this->provider);
    }

    /** @test */
    public function it_returns_order_item_unit_total_to_refund(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);

        $this->orderItemUnitRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($orderItemUnit);

        $orderItemUnit
            ->expects(self::once())
            ->method('getTotal')
            ->willReturn(1000);

        $result = $this->provider->getRefundUnitTotal(1);

        self::assertSame(1000, $result);
    }

    /** @test */
    public function it_throws_exception_if_there_is_no_order_item_unit_with_given_id(): void
    {
        $this->orderItemUnitRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->provider->getRefundUnitTotal(1);
    }
}

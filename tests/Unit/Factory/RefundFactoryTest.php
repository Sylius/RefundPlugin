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

namespace Tests\Sylius\RefundPlugin\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\Refund;
use Sylius\RefundPlugin\Factory\RefundFactory;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundFactoryTest extends TestCase
{
    private RefundFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new RefundFactory(Refund::class);
    }

    public function testItImplementsRefundFactoryInterface(): void
    {
        self::assertInstanceOf(RefundFactoryInterface::class, $this->factory);
    }

    public function testItAllowsToCreateRefundWithGivenData(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $result = $this->factory->createWithData($order, 1, 1000, RefundType::orderItemUnit());

        self::assertEquals(new Refund($order, 1000, 1, RefundType::orderItemUnit()), $result);
    }

    public function testItThrowsExceptionIfItTriesToCreateDefaultRefundWithoutData(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->createNew();
    }
}

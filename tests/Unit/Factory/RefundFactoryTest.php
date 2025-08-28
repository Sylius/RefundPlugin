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

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function it_implements_refund_factory_interface(): void
    {
        self::assertInstanceOf(RefundFactoryInterface::class, $this->factory);
    }

    #[Test]
    public function it_allows_to_create_refund_with_given_data(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $result = $this->factory->createWithData($order, 1, 1000, RefundType::orderItemUnit());

        self::assertEquals(new Refund($order, 1000, 1, RefundType::orderItemUnit()), $result);
    }

    #[Test]
    public function it_throws_exception_if_it_tries_to_create_default_refund_without_data(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->createNew();
    }
}

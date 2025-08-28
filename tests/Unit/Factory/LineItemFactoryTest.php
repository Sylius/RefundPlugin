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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Factory\LineItemFactory;

final class LineItemFactoryTest extends TestCase
{
    private LineItemFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new LineItemFactory(LineItem::class);
    }

    #[Test]
    public function it_is_a_resource_factory(): void
    {
        self::assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    #[Test]
    public function it_throws_an_exception_when_trying_to_create_a_new_line_item_without_data(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->createNew();
    }

    #[Test]
    public function it_creates_a_new_line_item_without_tax_rate(): void
    {
        $result = $this->factory->createWithData('T-Shirt', 2, 1000, 1200, 2000, 2400, 400);

        self::assertInstanceOf(LineItem::class, $result);
    }

    #[Test]
    public function it_creates_a_new_line_item_with_all_data(): void
    {
        $result = $this->factory->createWithData('T-Shirt', 2, 1000, 1200, 2000, 2400, 400, '0.2');

        self::assertInstanceOf(LineItem::class, $result);
    }
}

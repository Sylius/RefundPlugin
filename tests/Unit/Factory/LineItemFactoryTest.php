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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Factory\LineItemFactory;

final class LineItemFactoryTest extends TestCase
{
    private LineItemFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new LineItemFactory(LineItem::class);
    }

    public function testItIsAResourceFactory(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    public function testItThrowsAnExceptionWhenTryingToCreateANewLineItemWithoutData(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->createNew();
    }

    public function testItCreatesANewLineItemWithoutTaxRate(): void
    {
        $result = $this->factory->createWithData('T-Shirt', 2, 1000, 1200, 2000, 2400, 400);

        $this->assertInstanceOf(LineItem::class, $result);
    }

    public function testItCreatesANewLineItemWithAllData(): void
    {
        $result = $this->factory->createWithData('T-Shirt', 2, 1000, 1200, 2000, 2400, 400, '0.2');

        $this->assertInstanceOf(LineItem::class, $result);
    }
}
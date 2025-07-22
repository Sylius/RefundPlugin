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

namespace Tests\Sylius\RefundPlugin\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Exception\LineItemsCannotBeMerged;

final class LineItemTest extends TestCase
{
    private LineItem $lineItem;

    protected function setUp(): void
    {
        $this->lineItem = new LineItem('Mjolnir', 2, 1000, 1100, 2000, 2200, 200, '10%');
    }

    /** @test */
    function it_implements_line_item_interface(): void
    {
        $this->assertInstanceOf(LineItemInterface::class, $this->lineItem);
    }

    /** @test */
    function it_implements_resource_interface(): void
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->lineItem);
    }

    /** @test */
    function it_has_no_id_by_default(): void
    {
        $this->assertNull($this->lineItem->getId());
        $this->assertNull($this->lineItem->id());
    }

    /** @test */
    function it_has_proper_line_item_data(): void
    {
        $this->assertEquals('Mjolnir', $this->lineItem->name());
        $this->assertEquals(2, $this->lineItem->quantity());
        $this->assertEquals(1000, $this->lineItem->unitNetPrice());
        $this->assertEquals(1100, $this->lineItem->unitGrossPrice());
        $this->assertEquals(2000, $this->lineItem->netValue());
        $this->assertEquals(2200, $this->lineItem->grossValue());
        $this->assertEquals(200, $this->lineItem->taxAmount());
        $this->assertEquals('10%', $this->lineItem->taxRate());
    }

    /** @test */
    function it_merges_with_another_line_item(): void
    {
        $newLineItem = $this->createMock(LineItemInterface::class);
        $newLineItem->method('name')->willReturn('Mjolnir');
        $newLineItem->method('quantity')->willReturn(1);
        $newLineItem->method('unitNetPrice')->willReturn(1000);
        $newLineItem->method('unitGrossPrice')->willReturn(1100);
        $newLineItem->method('netValue')->willReturn(1000);
        $newLineItem->method('grossValue')->willReturn(1100);
        $newLineItem->method('taxAmount')->willReturn(100);
        $newLineItem->method('taxRate')->willReturn('10%');

        $this->lineItem->merge($newLineItem);

        $this->assertEquals(3, $this->lineItem->quantity());
        $this->assertEquals(3000, $this->lineItem->netValue());
        $this->assertEquals(3300, $this->lineItem->grossValue());
        $this->assertEquals(300, $this->lineItem->taxAmount());
    }

    /** @test */
    function it_throws_an_exception_if_another_line_item_is_different_during_merging(): void
    {
        $this->expectException(LineItemsCannotBeMerged::class);

        $newLineItem = $this->createMock(LineItemInterface::class);
        $newLineItem->method('name')->willReturn('Stormbreaker');
        $newLineItem->method('unitNetPrice')->willReturn(1000);
        $newLineItem->method('unitGrossPrice')->willReturn(1100);
        $newLineItem->method('taxRate')->willReturn('10%');

        $this->lineItem->merge($newLineItem);
    }

    /** @test */
    function it_compares_with_another_line_item(): void
    {
        $theSameLineItem = $this->createMock(LineItemInterface::class);
        $theSameLineItem->method('name')->willReturn('Mjolnir');
        $theSameLineItem->method('unitNetPrice')->willReturn(1000);
        $theSameLineItem->method('unitGrossPrice')->willReturn(1100);
        $theSameLineItem->method('taxRate')->willReturn('10%');

        $differentLineItem = $this->createMock(LineItemInterface::class);
        $differentLineItem->method('name')->willReturn('Stormbreaker');
        $differentLineItem->method('unitNetPrice')->willReturn(1000);
        $differentLineItem->method('unitGrossPrice')->willReturn(1100);
        $differentLineItem->method('taxRate')->willReturn('10%');

        $this->assertTrue($this->lineItem->compare($theSameLineItem));
        $this->assertFalse($this->lineItem->compare($differentLineItem));
    }
}
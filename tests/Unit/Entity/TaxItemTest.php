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
use Sylius\RefundPlugin\Entity\TaxItem;
use Sylius\RefundPlugin\Entity\TaxItemInterface;

final class TaxItemTest extends TestCase
{
    private TaxItem $taxItem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxItem = new TaxItem('VAT', 100);
    }

    /** @test */
    function it_implements_tax_item_interface(): void
    {
        self::assertInstanceOf(TaxItemInterface::class, $this->taxItem);
    }

    /** @test */
    function it_has_no_id_by_default(): void
    {
        self::assertNull($this->taxItem->getId());
        self::assertNull($this->taxItem->id());
    }

    /** @test */
    function it_has_a_label(): void
    {
        self::assertEquals('VAT', $this->taxItem->label());
    }

    /** @test */
    function it_has_an_amount(): void
    {
        self::assertEquals(100, $this->taxItem->amount());
    }
}

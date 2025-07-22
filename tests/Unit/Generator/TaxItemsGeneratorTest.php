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

namespace Sylius\RefundPlugin\Tests\Unit\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\TaxItem;
use Sylius\RefundPlugin\Generator\TaxItemsGenerator;
use Sylius\RefundPlugin\Generator\TaxItemsGeneratorInterface;

final class TaxItemsGeneratorTest extends TestCase
{
    private TaxItemsGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new TaxItemsGenerator();
    }

    public function testItImplementsTaxItemsGeneratorInterface(): void
    {
        $this->assertInstanceOf(TaxItemsGeneratorInterface::class, $this->generator);
    }

    public function testItGeneratesTaxItems(): void
    {
        $firstLineItem = $this->createMock(LineItemInterface::class);
        $secondLineItem = $this->createMock(LineItemInterface::class);

        $firstLineItem->expects($this->once())
            ->method('taxRate')
            ->willReturn('VAT');

        $firstLineItem->expects($this->once())
            ->method('taxAmount')
            ->willReturn(500);

        $secondLineItem->expects($this->once())
            ->method('taxRate')
            ->willReturn('VAT');

        $secondLineItem->expects($this->once())
            ->method('taxAmount')
            ->willReturn(800);

        $result = $this->generator->generate([$firstLineItem, $secondLineItem]);

        $this->assertEquals([new TaxItem('VAT', 1300)], $result);
    }
}
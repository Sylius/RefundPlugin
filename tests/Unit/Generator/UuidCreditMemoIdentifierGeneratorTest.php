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

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface;
use Sylius\RefundPlugin\Generator\UuidCreditMemoIdentifierGenerator;

final class UuidCreditMemoIdentifierGeneratorTest extends TestCase
{
    private UuidCreditMemoIdentifierGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new UuidCreditMemoIdentifierGenerator();
    }

    public function testItIsInitializable(): void
    {
        $this->assertInstanceOf(UuidCreditMemoIdentifierGenerator::class, $this->generator);
    }

    public function testItImplementsCreditMemoIdentifierGeneratorInterface(): void
    {
        $this->assertInstanceOf(CreditMemoIdentifierGeneratorInterface::class, $this->generator);
    }

    public function testItReturnsTwoDifferentStringsOnSubsequentCalls(): void
    {
        $firstGenerated = $this->generator->generate();
        $secondGenerated = $this->generator->generate();

        $this->assertNotSame($firstGenerated, $secondGenerated);
    }
}
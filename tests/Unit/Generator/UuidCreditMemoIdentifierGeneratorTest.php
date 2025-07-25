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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface;
use Sylius\RefundPlugin\Generator\UuidCreditMemoIdentifierGenerator;

final class UuidCreditMemoIdentifierGeneratorTest extends TestCase
{
    private UuidCreditMemoIdentifierGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new UuidCreditMemoIdentifierGenerator();
    }

    #[Test]
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(UuidCreditMemoIdentifierGenerator::class, $this->generator);
    }

    #[Test]
    public function it_implements_credit_memo_identifier_generator_interface(): void
    {
        self::assertInstanceOf(CreditMemoIdentifierGeneratorInterface::class, $this->generator);
    }

    #[Test]
    public function it_returns_two_different_strings_on_subsequent_calls(): void
    {
        $firstGenerated = $this->generator->generate();
        $secondGenerated = $this->generator->generate();

        self::assertNotSame($firstGenerated, $secondGenerated);
    }
}

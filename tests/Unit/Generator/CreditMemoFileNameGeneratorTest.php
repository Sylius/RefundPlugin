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
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGenerator;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;

final class CreditMemoFileNameGeneratorTest extends TestCase
{
    private CreditMemoFileNameGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new CreditMemoFileNameGenerator();
    }

    public function testItImplementsCreditMemoFileNameGeneratorInterface(): void
    {
        self::assertInstanceOf(CreditMemoFileNameGeneratorInterface::class, $this->generator);
    }

    public function testItGeneratesCreditMemoFileNameBasedOnItsNumber(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemo->expects(self::once())
            ->method('getNumber')
            ->willReturn('2018/05/000000006');

        $result = $this->generator->generateForPdf($creditMemo);

        self::assertSame('2018_05_000000006.pdf', $result);
    }
}

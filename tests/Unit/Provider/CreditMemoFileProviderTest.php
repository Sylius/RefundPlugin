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

namespace Tests\Sylius\RefundPlugin\Unit\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Provider\CreditMemoFileProvider;
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;

final class CreditMemoFileProviderTest extends TestCase
{
    private CreditMemoFileNameGeneratorInterface&MockObject $creditMemoFileNameGenerator;
    private CreditMemoFileManagerInterface&MockObject $creditMemoFileManager;
    private CreditMemoFileProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->creditMemoFileNameGenerator = $this->createMock(CreditMemoFileNameGeneratorInterface::class);
        $this->creditMemoFileManager = $this->createMock(CreditMemoFileManagerInterface::class);
        $this->provider = new CreditMemoFileProvider($this->creditMemoFileNameGenerator, $this->creditMemoFileManager);
    }

    /** @test */
    function it_is_initializable(): void
    {
        self::assertInstanceOf(CreditMemoFileProvider::class, $this->provider);
    }

    /** @test */
    function it_implements_credit_memo_file_provider_interface(): void
    {
        self::assertInstanceOf(CreditMemoFileProviderInterface::class, $this->provider);
    }

    /** @test */
    function it_provides_credit_memo_pdf_for_credit_memo(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);

        $this->creditMemoFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($creditMemo)
            ->willReturn('credit_memo.pdf');

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $this->creditMemoFileManager
            ->expects(self::once())
            ->method('get')
            ->with('credit_memo.pdf')
            ->willReturn($creditMemoPdf);

        $result = $this->provider->provide($creditMemo);

        self::assertEquals($creditMemoPdf, $result);
    }
}

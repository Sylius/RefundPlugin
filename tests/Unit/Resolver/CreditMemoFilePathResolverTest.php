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

namespace Tests\Sylius\RefundPlugin\Unit\Resolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolver;
use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface;

final class CreditMemoFilePathResolverTest extends TestCase
{
    private CreditMemoFilePathResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new CreditMemoFilePathResolver('/path/to/credit_memos');
    }

    #[Test]
    public function it_implements_credit_memo_file_path_resolver_interface(): void
    {
        self::assertInstanceOf(CreditMemoFilePathResolverInterface::class, $this->resolver);
    }

    #[Test]
    public function it_resolves_credit_memo_pdf_file_path(): void
    {
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $result = $this->resolver->resolve($creditMemoPdf);

        self::assertEquals('/path/to/credit_memos/credit_memo.pdf', $result);
    }

    #[Test]
    public function it_resolves_credit_memo_pdf_file_path_using_pdf_bundle(): void
    {
        $pdfFileManager = $this->createMock(PdfFileManagerInterface::class);
        $resolver = new CreditMemoFilePathResolver($pdfFileManager);

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $pdfFileManager
            ->expects(self::once())
            ->method('resolveLocalPath')
            ->with('credit_memo.pdf', 'sylius_refund')
            ->willReturn('/cached/path/credit_memo.pdf');

        $result = $resolver->resolve($creditMemoPdf);

        self::assertEquals('/cached/path/credit_memo.pdf', $result);
    }
}

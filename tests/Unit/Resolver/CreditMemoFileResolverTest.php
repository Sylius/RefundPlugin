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

use Gaufrette\Exception\FileNotFound;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface;
use Sylius\PdfGenerationBundle\Core\Model\PdfFile;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;
use Sylius\RefundPlugin\Repository\CreditMemoRepositoryInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolver;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;

final class CreditMemoFileResolverTest extends TestCase
{
    private CreditMemoRepositoryInterface&MockObject $creditMemoRepository;

    private CreditMemoFileProviderInterface&MockObject $creditMemoFileProvider;

    private CreditMemoPdfFileGeneratorInterface&MockObject $creditMemoPdfFileGenerator;

    private CreditMemoFileManagerInterface&MockObject $creditMemoFileManager;

    private CreditMemoFileResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->creditMemoRepository = $this->createMock(CreditMemoRepositoryInterface::class);
        $this->creditMemoFileProvider = $this->createMock(CreditMemoFileProviderInterface::class);
        $this->creditMemoPdfFileGenerator = $this->createMock(CreditMemoPdfFileGeneratorInterface::class);
        $this->creditMemoFileManager = $this->createMock(CreditMemoFileManagerInterface::class);

        $this->resolver = new CreditMemoFileResolver(
            $this->creditMemoRepository,
            $this->creditMemoFileProvider,
            $this->creditMemoPdfFileGenerator,
            $this->creditMemoFileManager,
        );
    }

    #[Test]
    public function it_implements_credit_memo_file_resolver_interface(): void
    {
        self::assertInstanceOf(CreditMemoFileResolverInterface::class, $this->resolver);
    }

    #[Test]
    public function it_resolves_credit_memo_pdf_for_credit_memo(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $this->creditMemoFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($creditMemo)
            ->willReturn($creditMemoPdf);

        $result = $this->resolver->resolveByCreditMemo($creditMemo);

        self::assertEquals($creditMemoPdf, $result);
    }

    #[Test]
    public function it_resolves_credit_memo_pdf_if_it_does_not_exist(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $creditMemo
            ->expects(self::once())
            ->method('getId')
            ->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->creditMemoFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($creditMemo)
            ->willThrowException(new FileNotFound('file'));

        $this->creditMemoPdfFileGenerator
            ->expects(self::once())
            ->method('generate')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemoPdf);

        $this->creditMemoFileManager
            ->expects(self::once())
            ->method('save')
            ->with($creditMemoPdf);

        $result = $this->resolver->resolveByCreditMemo($creditMemo);

        self::assertEquals($creditMemoPdf, $result);
    }

    #[Test]
    public function it_resolves_credit_memo_pdf_by_credit_memo_id(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $this->creditMemoRepository
            ->expects(self::once())
            ->method('find')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemo);

        $this->creditMemoFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($creditMemo)
            ->willReturn($creditMemoPdf);

        $result = $this->resolver->resolveById('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        self::assertEquals($creditMemoPdf, $result);
    }

    #[Test]
    public function it_resolves_credit_memo_pdf_by_its_id_if_it_does_not_exist(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $creditMemo
            ->expects(self::once())
            ->method('getId')
            ->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->creditMemoRepository
            ->expects(self::once())
            ->method('find')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemo);

        $this->creditMemoFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($creditMemo)
            ->willThrowException(new FileNotFound('file'));

        $this->creditMemoPdfFileGenerator
            ->expects(self::once())
            ->method('generate')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemoPdf);

        $this->creditMemoFileManager
            ->expects(self::once())
            ->method('save')
            ->with($creditMemoPdf);

        $result = $this->resolver->resolveById('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        self::assertEquals($creditMemoPdf, $result);
    }

    #[Test]
    public function it_resolves_existing_credit_memo_pdf_using_pdf_bundle(): void
    {
        $pdfFileManager = $this->createMock(PdfFileManagerInterface::class);
        $creditMemoFileNameGenerator = $this->createMock(CreditMemoFileNameGeneratorInterface::class);

        $resolver = new CreditMemoFileResolver(
            $this->creditMemoRepository,
            $this->creditMemoFileProvider,
            $this->creditMemoPdfFileGenerator,
            $pdfFileManager,
            $creditMemoFileNameGenerator,
        );

        $creditMemo = $this->createMock(CreditMemoInterface::class);

        $creditMemoFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($creditMemo)
            ->willReturn('credit_memo.pdf');

        $pdfFileManager
            ->expects(self::once())
            ->method('has')
            ->with('credit_memo.pdf', 'sylius_refund')
            ->willReturn(true);

        $pdfFileManager
            ->expects(self::once())
            ->method('get')
            ->with('credit_memo.pdf', 'sylius_refund')
            ->willReturn(new PdfFile('credit_memo.pdf', 'CONTENT'));

        $result = $resolver->resolveByCreditMemo($creditMemo);

        self::assertEquals(new CreditMemoPdf('credit_memo.pdf', 'CONTENT'), $result);
    }

    #[Test]
    public function it_generates_and_saves_credit_memo_pdf_using_pdf_bundle_when_not_found(): void
    {
        $pdfFileManager = $this->createMock(PdfFileManagerInterface::class);
        $creditMemoFileNameGenerator = $this->createMock(CreditMemoFileNameGeneratorInterface::class);

        $resolver = new CreditMemoFileResolver(
            $this->creditMemoRepository,
            $this->creditMemoFileProvider,
            $this->creditMemoPdfFileGenerator,
            $pdfFileManager,
            $creditMemoFileNameGenerator,
        );

        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $creditMemo
            ->expects(self::once())
            ->method('getId')
            ->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $creditMemoFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($creditMemo)
            ->willReturn('credit_memo.pdf');

        $pdfFileManager
            ->expects(self::once())
            ->method('has')
            ->with('credit_memo.pdf', 'sylius_refund')
            ->willReturn(false);

        $this->creditMemoPdfFileGenerator
            ->expects(self::once())
            ->method('generate')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemoPdf);

        $pdfFileManager
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(fn (PdfFile $f) => $f->filename() === 'credit_memo.pdf' && $f->content() === 'CONTENT'),
                'sylius_refund',
            );

        $result = $resolver->resolveByCreditMemo($creditMemo);

        self::assertEquals($creditMemoPdf, $result);
    }
}

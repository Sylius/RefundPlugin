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
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;
use Sylius\RefundPlugin\Repository\CreditMemoRepositoryInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolver;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;

final class CreditMemoFileResolverTest extends TestCase
{
    private CreditMemoRepositoryInterface $creditMemoRepository;
    private CreditMemoFileProviderInterface $creditMemoFileProvider;
    private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator;
    private CreditMemoFileManagerInterface $creditMemoFileManager;
    private CreditMemoFileResolver $resolver;

    protected function setUp(): void
    {
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

    /** @test */
    public function it_implements_credit_memo_file_resolver_interface(): void
    {
        $this->assertInstanceOf(CreditMemoFileResolverInterface::class, $this->resolver);
    }

    /** @test */
    public function it_resolves_credit_memo_pdf_for_credit_memo(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $this->creditMemoFileProvider
            ->expects($this->once())
            ->method('provide')
            ->with($creditMemo)
            ->willReturn($creditMemoPdf);

        $result = $this->resolver->resolveByCreditMemo($creditMemo);

        $this->assertEquals($creditMemoPdf, $result);
    }

    /** @test */
    public function it_resolves_credit_memo_pdf_if_it_does_not_exist(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $creditMemo
            ->expects($this->once())
            ->method('getId')
            ->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->creditMemoFileProvider
            ->expects($this->once())
            ->method('provide')
            ->with($creditMemo)
            ->willThrowException(new FileNotFound('file'));

        $this->creditMemoPdfFileGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemoPdf);

        $this->creditMemoFileManager
            ->expects($this->once())
            ->method('save')
            ->with($creditMemoPdf);

        $result = $this->resolver->resolveByCreditMemo($creditMemo);

        $this->assertEquals($creditMemoPdf, $result);
    }

    /** @test */
    public function it_resolves_credit_memo_pdf_by_credit_memo_id(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $this->creditMemoRepository
            ->expects($this->once())
            ->method('find')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemo);

        $this->creditMemoFileProvider
            ->expects($this->once())
            ->method('provide')
            ->with($creditMemo)
            ->willReturn($creditMemoPdf);

        $result = $this->resolver->resolveById('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->assertEquals($creditMemoPdf, $result);
    }

    /** @test */
    public function it_resolves_credit_memo_pdf_by_its_id_if_it_does_not_exist(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $creditMemo
            ->expects($this->once())
            ->method('getId')
            ->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->creditMemoRepository
            ->expects($this->once())
            ->method('find')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemo);

        $this->creditMemoFileProvider
            ->expects($this->once())
            ->method('provide')
            ->with($creditMemo)
            ->willThrowException(new FileNotFound('file'));

        $this->creditMemoPdfFileGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemoPdf);

        $this->creditMemoFileManager
            ->expects($this->once())
            ->method('save')
            ->with($creditMemoPdf);

        $result = $this->resolver->resolveById('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->assertEquals($creditMemoPdf, $result);
    }
}
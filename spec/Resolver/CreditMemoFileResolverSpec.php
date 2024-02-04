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

namespace spec\Sylius\RefundPlugin\Resolver;

use Gaufrette\Exception\FileNotFound;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;
use Sylius\RefundPlugin\Repository\CreditMemoRepositoryInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;

final class CreditMemoFileResolverSpec extends ObjectBehavior
{
    function let(
        CreditMemoRepositoryInterface $creditMemoRepository,
        CreditMemoFileProviderInterface $creditMemoFileProvider,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
    ): void {
        $this->beConstructedWith(
            $creditMemoRepository,
            $creditMemoFileProvider,
            $creditMemoPdfFileGenerator,
            $creditMemoFileManager,
        );
    }

    function it_implements_credit_memo_file_resolver_interface(): void
    {
        $this->shouldImplement(CreditMemoFileResolverInterface::class);
    }

    function it_resolves_credit_memo_pdf_for_credit_memo(
        CreditMemoFileProviderInterface $creditMemoFileProvider,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $creditMemoFileProvider->provide($creditMemo)->willReturn($creditMemoPdf);

        $this->resolveByCreditMemo($creditMemo)->shouldBeLike($creditMemoPdf);
    }

    function it_resolves_credit_memo_pdf_if_it_does_not_exist(
        CreditMemoFileProviderInterface $creditMemoFileProvider,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemo->getId()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $creditMemoFileProvider->provide($creditMemo)->willThrow(FileNotFound::class);

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $creditMemoPdfFileGenerator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn($creditMemoPdf);
        $creditMemoFileManager->save($creditMemoPdf)->shouldBeCalled();

        $this->resolveByCreditMemo($creditMemo)->shouldBeLike($creditMemoPdf);
    }

    function it_resolves_credit_memo_pdf_by_credit_memo_id(
        CreditMemoRepositoryInterface $creditMemoRepository,
        CreditMemoFileProviderInterface $creditMemoFileProvider,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemoRepository->find('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn($creditMemo);

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $creditMemoFileProvider->provide($creditMemo)->willReturn($creditMemoPdf);

        $this->resolveById('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->shouldBeLike($creditMemoPdf);
    }

    function it_resolves_credit_memo_pdf_by_its_id_if_it_does_not_exist(
        CreditMemoRepositoryInterface $creditMemoRepository,
        CreditMemoFileProviderInterface $creditMemoFileProvider,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemo->getId()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        $creditMemoRepository->find('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn($creditMemo);

        $creditMemoFileProvider->provide($creditMemo)->willThrow(FileNotFound::class);

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $creditMemoPdfFileGenerator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn($creditMemoPdf);
        $creditMemoFileManager->save($creditMemoPdf)->shouldBeCalled();

        $this->resolveById('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->shouldBeLike($creditMemoPdf);
    }
}

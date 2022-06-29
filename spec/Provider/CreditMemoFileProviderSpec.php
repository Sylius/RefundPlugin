<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use Gaufrette\Exception\FileNotFound;
use Gaufrette\File;
use Gaufrette\FilesystemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;

final class CreditMemoFileProviderSpec extends ObjectBehavior
{
    function let(
        CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
    ): void {
        $this->beConstructedWith(
            $creditMemoFileNameGenerator,
            $creditMemoPdfFileGenerator,
            $creditMemoFileManager,
            '/path/to/credit_memos',
        );
    }

    function it_implements_credit_memo_file_provider_interface(): void
    {
        $this->shouldImplement(CreditMemoFileProviderInterface::class);
    }

    function it_provides_credit_memo_pdf_for_invoice(
        CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
        CreditMemoInterface $creditMemo,
        File $creditMemoFile,
    ): void {
        $creditMemoFileNameGenerator->generateForPdf($creditMemo)->willReturn('credit_memo.pdf');

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $creditMemoFileManager->get('credit_memo.pdf')->willReturn($creditMemoPdf);

        $creditMemoPdf->setFullPath('/path/to/credit_memos/credit_memo.pdf');

        $this->provide($creditMemo)->shouldBeLike($creditMemoPdf);
    }

    function it_generates_and_provides_credit_memo_if_it_does_not_exist(
        CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemoFileNameGenerator->generateForPdf($creditMemo)->willReturn('credit_memo.pdf');

        $creditMemoFileManager->get('credit_memo.pdf')->willThrow(FileNotFound::class);

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $creditMemoPdf->setFullPath('/path/to/credit_memos/credit_memo.pdf');

        $creditMemo->getId()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $creditMemoPdfFileGenerator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn($creditMemoPdf);
        $creditMemoFileManager->save($creditMemoPdf)->shouldBeCalled();

        $this->provide($creditMemo)->shouldBeLike($creditMemoPdf);
    }
}

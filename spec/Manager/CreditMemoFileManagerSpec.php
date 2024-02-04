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

namespace spec\Sylius\RefundPlugin\Manager;

use Gaufrette\Exception\FileNotFound;
use Gaufrette\File;
use Gaufrette\FilesystemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoFileManagerSpec extends ObjectBehavior
{
    function let(FilesystemInterface $filesystem): void
    {
        $this->beConstructedWith($filesystem);
    }

    function it_implements_credit_memo_file_manager_interface(): void
    {
        $this->shouldImplement(CreditMemoFileManagerInterface::class);
    }

    function it_saves_credit_memo_pdf_in_given_filesystem(FilesystemInterface $filesystem): void
    {
        $filesystem->write('2018_05_000000006.pdf', 'CONTENT')->shouldBeCalled();

        $this->save(new CreditMemoPdf('2018_05_000000006.pdf', 'CONTENT'));
    }

    function it_removes_credit_memo_pdf_from_given_filesystem(FilesystemInterface $filesystem): void
    {
        $filesystem->delete('2018_05_000000006.pdf')->shouldBeCalled();

        $this->remove(new CreditMemoPdf('2018_05_000000006.pdf', 'CONTENT'));
    }

    function it_returns_credit_memo_pdf_for_given_file_name_from_filesystem(
        FilesystemInterface $filesystem,
        File $file,
    ): void {
        $file->getContent()->willReturn('CONTENT');
        $filesystem->get('2018_05_000000006.pdf')->willReturn($file);

        $this->get('2018_05_000000006.pdf')->shouldBeLike(new CreditMemoPdf('2018_05_000000006.pdf', 'CONTENT'));
    }

    function it_throws_an_exception_if_there_is_no_file_for_given_file_name_in_filesystem(
        FilesystemInterface $filesystem,
    ): void {
        $filesystem->get('2018_05_000000006.pdf')->willThrow(FileNotFound::class);

        $this->shouldThrow(FileNotFound::class)->during('get', ['2018_05_000000006.pdf']);
    }
}

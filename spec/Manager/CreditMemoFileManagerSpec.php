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

namespace spec\Sylius\RefundPlugin\Manager;

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
}

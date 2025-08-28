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

namespace Tests\Sylius\RefundPlugin\Unit\Manager;

use Gaufrette\Exception\FileNotFound;
use Gaufrette\File;
use Gaufrette\FilesystemInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Manager\CreditMemoFileManager;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoFileManagerTest extends TestCase
{
    private FilesystemInterface&MockObject $filesystem;

    private CreditMemoFileManager $creditMemoFileManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = $this->createMock(FilesystemInterface::class);
        $this->creditMemoFileManager = new CreditMemoFileManager($this->filesystem);
    }

    #[Test]
    public function it_implements_credit_memo_file_manager_interface(): void
    {
        self::assertInstanceOf(CreditMemoFileManagerInterface::class, $this->creditMemoFileManager);
    }

    #[Test]
    public function it_saves_credit_memo_pdf_in_given_filesystem(): void
    {
        $this->filesystem
            ->expects(self::once())
            ->method('write')
            ->with('2018_05_000000006.pdf', 'CONTENT');

        $this->creditMemoFileManager->save(new CreditMemoPdf('2018_05_000000006.pdf', 'CONTENT'));
    }

    #[Test]
    public function it_removes_credit_memo_pdf_from_given_filesystem(): void
    {
        $this->filesystem
            ->expects(self::once())
            ->method('delete')
            ->with('2018_05_000000006.pdf');

        $this->creditMemoFileManager->remove(new CreditMemoPdf('2018_05_000000006.pdf', 'CONTENT'));
    }

    #[Test]
    public function it_returns_credit_memo_pdf_for_given_file_name_from_filesystem(): void
    {
        $file = $this->createMock(File::class);
        $file
            ->expects(self::once())
            ->method('getContent')
            ->willReturn('CONTENT');

        $this->filesystem
            ->expects(self::once())
            ->method('get')
            ->with('2018_05_000000006.pdf')
            ->willReturn($file);

        $result = $this->creditMemoFileManager->get('2018_05_000000006.pdf');

        self::assertEquals(new CreditMemoPdf('2018_05_000000006.pdf', 'CONTENT'), $result);
    }

    #[Test]
    public function it_throws_an_exception_if_there_is_no_file_for_given_file_name_in_filesystem(): void
    {
        $this->filesystem
            ->expects(self::once())
            ->method('get')
            ->with('2018_05_000000006.pdf')
            ->willThrowException(new FileNotFound('2018_05_000000006.pdf'));

        $this->expectException(FileNotFound::class);

        $this->creditMemoFileManager->get('2018_05_000000006.pdf');
    }
}

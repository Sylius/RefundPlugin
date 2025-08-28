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

namespace Tests\Sylius\RefundPlugin\Unit;

use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Manager\CreditMemoFileManager;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoFileManagerTest extends TestCase
{
    #[Test]
    public function it_creates_file_in_given_filesystem(): void
    {
        $creditMemoFileManager = $this->prepareCreditMemoFileManager();

        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'test file content');
        $creditMemoFileManager->save($creditMemoPdf);

        self::assertFileExists('temp/credit-memo.pdf');
        self::assertEquals('test file content', file_get_contents('temp/credit-memo.pdf'));

        $this->clearTemporaryDirectory();
    }

    #[Test]
    public function it_removes_file_from_given_filesystem(): void
    {
        $creditMemoFileManager = $this->prepareCreditMemoFileManager();

        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'test file content');
        $creditMemoFileManager->save($creditMemoPdf);
        $creditMemoFileManager->remove($creditMemoPdf);

        self::assertFileDoesNotExist('temp/credit-memo.pdf');
    }

    #[Test]
    public function it_provides_file_from_given_filesystem(): void
    {
        $creditMemoFileManager = $this->prepareCreditMemoFileManager();

        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'test file content');
        $creditMemoFileManager->save($creditMemoPdf);
        $file = $creditMemoFileManager->get('credit-memo.pdf');

        self::assertEquals($creditMemoPdf, $file);

        $this->clearTemporaryDirectory();
    }

    private function prepareCreditMemoFileManager(): CreditMemoFileManagerInterface
    {
        $this->clearTemporaryDirectory();

        $adapter = new Local('temp', true);

        return new CreditMemoFileManager(new Filesystem($adapter));
    }

    private function clearTemporaryDirectory(): void
    {
        if (file_exists('temp/credit-memo.pdf')) {
            unlink('temp/credit-memo.pdf');
            rmdir('temp');

            return;
        }

        if (is_dir('temp')) {
            rmdir('temp');
        }
    }
}

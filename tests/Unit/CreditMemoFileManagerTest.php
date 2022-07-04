<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit;

use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Manager\CreditMemoFileManager;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoFileManagerTest extends TestCase
{
    /** @test */
    function it_creates_file_in_given_filesystem(): void
    {
        $creditMemoFileManager = $this->prepareCreditMemoFileManager();

        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'test file content');
        $creditMemoFileManager->save($creditMemoPdf);

        $this->assertFileExists('temp/credit-memo.pdf');
        $this->assertEquals('test file content', file_get_contents('temp/credit-memo.pdf'));

        $this->clearTemporaryDirectory();
    }

    /** @test */
    function it_removes_file_from_given_filesystem(): void
    {
        $creditMemoFileManager = $this->prepareCreditMemoFileManager();

        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'test file content');
        $creditMemoFileManager->save($creditMemoPdf);
        $creditMemoFileManager->remove($creditMemoPdf);

        $this->assertFileDoesNotExist('temp/credit-memo.pdf');
    }

    /** @test */
    function it_provides_file_from_given_filesystem(): void
    {
        $creditMemoFileManager = $this->prepareCreditMemoFileManager();

        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'test file content');
        $creditMemoFileManager->save($creditMemoPdf);
        $file = $creditMemoFileManager->get('credit-memo.pdf');

        $this->assertEquals($creditMemoPdf, $file);

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
        if (file_exists('temp/credit-memo.pdf')){
            unlink('temp/credit-memo.pdf');
            rmdir('temp');

            return;
        }

        if (is_dir('temp')){
            rmdir('temp');
        }
    }
}

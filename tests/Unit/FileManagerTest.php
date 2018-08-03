<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\File\FileManager;
use Sylius\RefundPlugin\File\FileManagerInterface;

final class FileManagerTest extends TestCase
{
    /** @test */
    function it_implements_file_manager_interface(): void
    {
        $this->assertInstanceOf(FileManagerInterface::class, new FileManager());
    }

    /** @test */
    function it_has_base_directory(): void
    {
        $this->assertEquals(__DIR__.'/', (new FileManager(__DIR__.'/'))->getBaseDirectory());
    }

    /** @test */
    function it_creates_file_in_base_directory(): void
    {
        $fileManager = new FileManager(__DIR__.'/');

        $fileManager->createWithContent('test.txt', 'test file content');

        $this->assertFileExists(__DIR__.'/test.txt');
        $this->assertEquals('test file content', file_get_contents(__DIR__.'/test.txt'));

        $fileManager->remove('test.txt');
    }

    /** @test */
    function it_removes_file_from_base_directory(): void
    {
        $fileManager = new FileManager(__DIR__.'/');

        $fileManager->createWithContent('file-to-remove.txt', 'test file content');
        $fileManager->remove('file-to-remove.txt');

        $this->assertFileNotExists(__DIR__.'/file-to-remove.txt');
    }
}

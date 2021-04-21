<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\File\TemporaryFileManager;
use Sylius\RefundPlugin\File\FileManagerInterface;

final class TemporaryFileManagerTest extends TestCase
{
    /** @test */
    function it_implements_file_manager_interface(): void
    {
        $this->assertInstanceOf(FileManagerInterface::class, new TemporaryFileManager());
    }

    /** @test */
    function it_returns_real_path_of_file(): void
    {
        $fileManager = new TemporaryFileManager();

        $fileManager->createWithContent('test.txt', 'test file content');

        $this->assertEquals(sys_get_temp_dir().'/test.txt', $fileManager->realPath('test.txt'));
    }

    /** @test */
    function it_creates_file_in_base_directory(): void
    {
        $fileManager = new TemporaryFileManager();

        $fileManager->createWithContent('test.txt', 'test file content');

        $this->assertFileExists(sys_get_temp_dir().'/test.txt');
        $this->assertEquals('test file content', file_get_contents(sys_get_temp_dir().'/test.txt'));

        $fileManager->remove('test.txt');
    }

    /** @test */
    function it_removes_file_from_base_directory(): void
    {
        $fileManager = new TemporaryFileManager();

        $fileManager->createWithContent('file-to-remove.txt', 'test file content');
        $fileManager->remove('file-to-remove.txt');

        $this->assertFileDoesNotExist(sys_get_temp_dir().'/file-to-remove.txt');
    }
}

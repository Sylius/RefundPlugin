<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\File;

final class FileManager implements FileManagerInterface
{
    /** @var string */
    private $baseDirectory;

    public function __construct(string $baseDirectory = '')
    {
        $this->baseDirectory = $baseDirectory;
    }

    public function createWithContent(string $fileName, string $content): void
    {
        file_put_contents($this->baseDirectory . $fileName, $content);
    }

    public function remove(string $fileName): void
    {
        unlink($this->baseDirectory . $fileName);
    }

    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }
}

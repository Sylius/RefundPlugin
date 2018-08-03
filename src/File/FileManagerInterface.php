<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\File;

interface FileManagerInterface
{
    public function createWithContent(string $fileName, string $content): void;

    public function remove(string $fileName): void;

    public function realPath(string $fileName): string;
}

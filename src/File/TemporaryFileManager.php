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

namespace Sylius\RefundPlugin\File;

/**
 * @deprecated since 1.5, to be removed in 2.0
 */
final class TemporaryFileManager implements FileManagerInterface
{
    private string $baseDirectory;

    public function __construct()
    {
        $this->baseDirectory = sys_get_temp_dir() . '/';
    }

    public function createWithContent(string $fileName, string $content): void
    {
        file_put_contents($this->baseDirectory . $fileName, $content);
    }

    public function remove(string $fileName): void
    {
        unlink($this->baseDirectory . $fileName);
    }

    public function realPath(string $fileName): string
    {
        return $this->baseDirectory . $fileName;
    }
}

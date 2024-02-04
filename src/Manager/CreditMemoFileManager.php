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

namespace Sylius\RefundPlugin\Manager;

use Gaufrette\FilesystemInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoFileManager implements CreditMemoFileManagerInterface
{
    public function __construct(private FilesystemInterface $filesystem)
    {
    }

    public function save(CreditMemoPdf $file): void
    {
        $this->filesystem->write($file->filename(), $file->content());
    }

    public function remove(CreditMemoPdf $file): void
    {
        $this->filesystem->delete($file->filename());
    }

    public function get(string $fileName): CreditMemoPdf
    {
        $file = $this->filesystem->get($fileName);

        return new CreditMemoPdf($fileName, $file->getContent());
    }
}

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
interface FileManagerInterface
{
    public function createWithContent(string $fileName, string $content): void;

    public function remove(string $fileName): void;

    public function realPath(string $fileName): string;
}

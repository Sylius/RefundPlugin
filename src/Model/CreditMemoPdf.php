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

namespace Sylius\RefundPlugin\Model;

final class CreditMemoPdf
{
    public function __construct(
        private string $filename,
        private string $content,
    ) {
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function content(): string
    {
        return $this->content;
    }
}

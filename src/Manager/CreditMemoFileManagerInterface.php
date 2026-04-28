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

use Sylius\RefundPlugin\Model\CreditMemoPdf;

/**
 * @deprecated since sylius/refund-plugin 2.1, use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface from sylius/pdf-generation-bundle instead.
 */
interface CreditMemoFileManagerInterface
{
    public function save(CreditMemoPdf $file): void;

    public function remove(CreditMemoPdf $file): void;

    public function get(string $fileName): CreditMemoPdf;
}

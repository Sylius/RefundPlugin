<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\RefundPlugin\Entity\CreditMemoInterface;

final class CreditMemoFileNameGenerator implements CreditMemoFileNameGeneratorInterface
{
    private const PDF_FILE_EXTENSION = '.pdf';

    public function generateForPdf(CreditMemoInterface $creditMemo): string
    {
        return str_replace('/', '_', $creditMemo->getNumber()) . self::PDF_FILE_EXTENSION;
    }
}

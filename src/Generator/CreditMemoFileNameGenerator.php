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

namespace Sylius\RefundPlugin\Generator;

use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Webmozart\Assert\Assert;

final class CreditMemoFileNameGenerator implements CreditMemoFileNameGeneratorInterface
{
    private const PDF_FILE_EXTENSION = '.pdf';

    public function generateForPdf(CreditMemoInterface $creditMemo): string
    {
        $number = $creditMemo->getNumber();
        Assert::notNull($number);

        return str_replace('/', '_', $number) . self::PDF_FILE_EXTENSION;
    }
}

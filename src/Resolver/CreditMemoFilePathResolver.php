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

namespace Sylius\RefundPlugin\Resolver;

use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final readonly class CreditMemoFilePathResolver implements CreditMemoFilePathResolverInterface
{
    public function __construct(private string|PdfFileManagerInterface $creditMemosPath)
    {
        if (is_string($this->creditMemosPath)) {
            trigger_deprecation(
                'sylius/refund-plugin',
                '2.1',
                'Passing a string value for $creditMemosPath argument to %s is deprecated and it will not be supported in 3.0, use an instance of %s instead.',
                self::class,
                PdfFileManagerInterface::class,
            );
        }
    }

    public function resolve(CreditMemoPdf $creditMemoPdf): string
    {
        if ($this->creditMemosPath instanceof PdfFileManagerInterface) {
            return $this->creditMemosPath->resolveLocalPath($creditMemoPdf->filename(), 'sylius_refund');
        }

        return $this->creditMemosPath . '/' . $creditMemoPdf->filename();
    }
}

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

use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoFilePathResolver implements CreditMemoFilePathResolverInterface
{
    public function __construct(private string $creditMemosPath)
    {
    }

    public function resolve(CreditMemoPdf $creditMemoPdf): string
    {
        return $this->creditMemosPath . '/' . $creditMemoPdf->filename();
    }
}

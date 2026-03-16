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

/**
 * @deprecated since sylius/refund-plugin 2.1, use sylius/pdf-generation-bundle's adapter options instead.
 */
interface PdfOptionsGeneratorInterface
{
    /** @return array<string, mixed> */
    public function generate(): array;
}

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

namespace Sylius\RefundPlugin\Provider;

use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoFileProvider implements CreditMemoFileProviderInterface
{
    public function __construct(
        private CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
        private CreditMemoFileManagerInterface $creditMemoFileManager,
    ) {
    }

    public function provide(CreditMemoInterface $creditMemo): CreditMemoPdf
    {
        $fileName = $this->creditMemoFileNameGenerator->generateForPdf($creditMemo);

        return $this->creditMemoFileManager->get($fileName);
    }
}

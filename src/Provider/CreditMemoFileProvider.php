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

namespace Sylius\RefundPlugin\Provider;

use Gaufrette\Exception\FileNotFound;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoFileProvider implements CreditMemoFileProviderInterface
{
    public function __construct(
        private CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
        private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        private CreditMemoFileManagerInterface $creditMemoFileManager,
        private string $creditMemosPath,
    ) {
    }

    public function provide(CreditMemoInterface $creditMemo): CreditMemoPdf
    {
        $fileName = $this->creditMemoFileNameGenerator->generateForPdf($creditMemo);

        try {
            $pdf = $this->creditMemoFileManager->get($fileName);
        } catch (FileNotFound) {
            $pdf = $this->creditMemoPdfFileGenerator->generate($creditMemo->getId());
            $this->creditMemoFileManager->save($pdf);
        }

        $pdf->setFullPath($this->creditMemosPath . '/' . $fileName);

        return $pdf;
    }
}

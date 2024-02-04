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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\Config\FileLocatorInterface;

final class CreditMemoPdfFileGenerator implements CreditMemoPdfFileGeneratorInterface
{
    public function __construct(
        private RepositoryInterface $creditMemoRepository,
        private FileLocatorInterface $fileLocator,
        private string $template,
        private string $creditMemoLogoPath,
        private TwigToPdfGeneratorInterface $twigToPdfGenerator,
        private CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
    ) {
    }

    public function generate(string $creditMemoId): CreditMemoPdf
    {
        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($creditMemoId);

        if ($creditMemo === null) {
            throw CreditMemoNotFound::withId($creditMemoId);
        }

        $pdf = $this->generateFromTemplate([
            'creditMemo' => $creditMemo,
            'creditMemoLogoPath' => $this->fileLocator->locate($this->creditMemoLogoPath),
        ]);

        return new CreditMemoPdf($this->generateFileName($creditMemo), $pdf);
    }

    private function generateFromTemplate(array $templateParams): string
    {
        return $this->twigToPdfGenerator->generate($this->template, $templateParams);
    }

    private function generateFileName(CreditMemoInterface $creditMemo): string
    {
        return $this->creditMemoFileNameGenerator->generateForPdf($creditMemo);
    }
}

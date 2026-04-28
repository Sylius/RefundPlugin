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

use Gaufrette\Exception\FileNotFound;
use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface;
use Sylius\PdfGenerationBundle\Core\Model\PdfFile;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;
use Sylius\RefundPlugin\Repository\CreditMemoRepositoryInterface;
use Webmozart\Assert\Assert;

final readonly class CreditMemoFileResolver implements CreditMemoFileResolverInterface
{
    public function __construct(
        private CreditMemoRepositoryInterface $creditMemoRepository,
        private CreditMemoFileProviderInterface $creditMemoFileProvider,
        private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        private CreditMemoFileManagerInterface|PdfFileManagerInterface $creditMemoFileManager,
        private ?CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator = null,
    ) {
        if ($this->creditMemoFileManager instanceof CreditMemoFileManagerInterface) {
            trigger_deprecation(
                'sylius/refund-plugin',
                '2.1',
                'Passing an instance of %s to %s is deprecated and it will not be supported in 3.0, use an instance of %s instead.',
                CreditMemoFileManagerInterface::class,
                self::class,
                PdfFileManagerInterface::class,
            );
        }
    }

    public function resolveByCreditMemo(CreditMemoInterface $creditMemo): CreditMemoPdf
    {
        if ($this->creditMemoFileManager instanceof PdfFileManagerInterface) {
            return $this->resolveByCreditMemoUsingPdfBundle($creditMemo);
        }

        try {
            $pdf = $this->creditMemoFileProvider->provide($creditMemo);
        } catch (FileNotFound) {
            $pdf = $this->creditMemoPdfFileGenerator->generate($creditMemo->getId());
            $this->creditMemoFileManager->save($pdf);
        }

        return $pdf;
    }

    public function resolveById(string $creditMemoId): CreditMemoPdf
    {
        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($creditMemoId);
        Assert::notNull($creditMemo);

        return $this->resolveByCreditMemo($creditMemo);
    }

    private function resolveByCreditMemoUsingPdfBundle(CreditMemoInterface $creditMemo): CreditMemoPdf
    {
        Assert::isInstanceOf($this->creditMemoFileManager, PdfFileManagerInterface::class);
        Assert::isInstanceOf($this->creditMemoFileNameGenerator, CreditMemoFileNameGeneratorInterface::class);

        $filename = $this->creditMemoFileNameGenerator->generateForPdf($creditMemo);

        if ($this->creditMemoFileManager->has($filename, 'sylius_refund')) {
            $pdfFile = $this->creditMemoFileManager->get($filename, 'sylius_refund');

            return new CreditMemoPdf($pdfFile->filename(), $pdfFile->content());
        }

        $pdf = $this->creditMemoPdfFileGenerator->generate($creditMemo->getId());
        $pdfFile = new PdfFile($pdf->filename(), $pdf->content());
        $this->creditMemoFileManager->save($pdfFile, 'sylius_refund');

        return $pdf;
    }
}

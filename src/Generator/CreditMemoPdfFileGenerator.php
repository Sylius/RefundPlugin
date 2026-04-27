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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\Config\FileLocatorInterface;

final readonly class CreditMemoPdfFileGenerator implements CreditMemoPdfFileGeneratorInterface
{
    public function __construct(
        private RepositoryInterface $creditMemoRepository,
        private FileLocatorInterface $fileLocator,
        private string $template,
        private string $creditMemoLogoPath,
        private TwigToPdfGeneratorInterface|TwigToPdfRendererInterface $twigToPdfRenderer,
        private CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
    ) {
        if ($this->twigToPdfRenderer instanceof TwigToPdfGeneratorInterface) {
            trigger_deprecation(
                'sylius/refund-plugin',
                '2.1',
                'Passing an instance of %s to %s is deprecated and it will not be supported in 3.0, use an instance of %s instead.',
                TwigToPdfGeneratorInterface::class,
                self::class,
                TwigToPdfRendererInterface::class,
            );
        }
    }

    public function generate(string $creditMemoId): CreditMemoPdf
    {
        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($creditMemoId);

        if ($creditMemo === null) {
            throw CreditMemoNotFound::withId($creditMemoId);
        }

        $logoPath = $this->fileLocator->locate($this->creditMemoLogoPath);

        $pdf = $this->generateFromTemplate([
            'creditMemo' => $creditMemo,
            'creditMemoLogoPath' => $logoPath,
            'creditMemoLogo' => $this->buildLogoDataUri($logoPath),
        ]);

        return new CreditMemoPdf($this->generateFileName($creditMemo), $pdf);
    }

    private function buildLogoDataUri(string $path): string
    {
        $mimeType = mime_content_type($path) ?: 'image/png';

        return sprintf('data:%s;base64,%s', $mimeType, base64_encode((string) file_get_contents($path)));
    }

    /** @param array<string, mixed> $templateParams */
    private function generateFromTemplate(array $templateParams): string
    {
        if ($this->twigToPdfRenderer instanceof TwigToPdfRendererInterface) {
            return $this->twigToPdfRenderer->render($this->template, $templateParams, 'sylius_refund');
        }

        return $this->twigToPdfRenderer->generate($this->template, $templateParams);
    }

    private function generateFileName(CreditMemoInterface $creditMemo): string
    {
        return $this->creditMemoFileNameGenerator->generateForPdf($creditMemo);
    }
}
